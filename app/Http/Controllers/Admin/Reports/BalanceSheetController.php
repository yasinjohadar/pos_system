<?php

namespace App\Http\Controllers\Admin\Reports;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use App\Services\Accounting\GeneralLedgerService;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reports-view');
    }

    public function index(Request $request, GeneralLedgerService $service)
    {
        $asOf = $request->input('as_of', now()->format('Y-m-d'));
        $balances = $service->getBalancesByType($asOf);

        $totalAssets = collect($balances[ChartOfAccount::TYPE_ASSET] ?? [])->sum('balance');
        $totalLiabilities = collect($balances[ChartOfAccount::TYPE_LIABILITY] ?? [])->sum('balance');
        $totalEquity = collect($balances[ChartOfAccount::TYPE_EQUITY] ?? [])->sum('balance');

        return view('admin.pages.reports.balance-sheet.index', compact(
            'asOf', 'balances', 'totalAssets', 'totalLiabilities', 'totalEquity'
        ));
    }
}
