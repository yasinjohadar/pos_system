<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\Accounting\GeneralLedgerService;
use Illuminate\Http\Request;

class GeneralLedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view');
    }

    public function index(Request $request, GeneralLedgerService $service)
    {
        $from = $request->input('from', now()->startOfMonth()->format('Y-m-d'));
        $to = $request->input('to', now()->format('Y-m-d'));
        $accountId = $request->input('account_id');
        $accounts = ChartOfAccount::where('is_active', true)->orderBy('code')->get();
        $lines = $service->getLedger($accountId ? (int) $accountId : null, $from, $to);

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.reports.general-ledger.partials.table-rows', compact('lines'))->render(),
            ]);
        }

        return view('admin.pages.reports.general-ledger.index', compact('lines', 'from', 'to', 'accounts', 'accountId'));
    }
}
