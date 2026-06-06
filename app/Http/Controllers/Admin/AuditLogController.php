<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Support\AuditLogPresenter;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditLogPresenter $presenter
    ) {
        $this->middleware('auth');
        $this->middleware('permission:manage_audit_logs')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $logs = $this->buildAuditQuery($request)->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);
        $modelTypes = AuditLog::query()->select('model_type')->distinct()->orderBy('model_type')->pluck('model_type');
        $actions = [
            AuditLog::ACTION_CREATE,
            AuditLog::ACTION_UPDATE,
            AuditLog::ACTION_DELETE,
            AuditLog::ACTION_CONFIRM,
            AuditLog::ACTION_CANCEL,
        ];

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.audit.partials.table-rows', [
                    'logs' => $logs,
                    'presenter' => $this->presenter,
                ])->render(),
                'pagination' => view('admin.pages.audit.partials.pagination', compact('logs'))->render(),
            ]);
        }

        return view('admin.pages.audit.index', compact('logs', 'users', 'modelTypes', 'actions'))
            ->with('presenter', $this->presenter);
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return response()->json($this->presenter->toDetailArray($auditLog));
    }

    private function buildAuditQuery(Request $request)
    {
        $query = AuditLog::query()->with('user')->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            if ($request->input('user_id') === 'system') {
                $query->whereNull('user_id');
            } else {
                $query->where('user_id', $request->input('user_id'));
            }
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->input('model_type'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->input('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->input('to'));
        }

        if ($request->filled('query')) {
            $search = trim((string) $request->input('query'));
            $query->where(function ($q) use ($search) {
                $q->where('model_id', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });

                foreach (config('audit.models', []) as $modelType => $meta) {
                    if (str_contains($this->presenter->modelLabel($modelType), $search)) {
                        $q->orWhere('model_type', $modelType);
                    }
                }
            });
        }

        return $query;
    }
}
