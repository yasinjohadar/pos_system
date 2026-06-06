<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\Accounting\GeneralLedgerService;
use Illuminate\Http\Request;

class AccountStatementController extends Controller
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

        $statement = null;
        if ($accountId) {
            $statement = $service->getAccountStatement((int) $accountId, $from, $to);
        }

        return view('admin.pages.reports.account-statement.index', compact('from', 'to', 'accounts', 'accountId', 'statement'));
    }
}
