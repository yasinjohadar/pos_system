<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage_audit_logs')->only(['index']);
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user')->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(25)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name']);

        $modelTypes = AuditLog::select('model_type')->distinct()->pluck('model_type');
        $actions = [AuditLog::ACTION_CREATE, AuditLog::ACTION_UPDATE, AuditLog::ACTION_DELETE, AuditLog::ACTION_CONFIRM, AuditLog::ACTION_CANCEL];

        return view('admin.pages.audit.index', compact('logs', 'users', 'modelTypes', 'actions'));
    }
}
