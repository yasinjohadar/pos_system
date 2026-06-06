<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FiscalYear;
use Illuminate\Http\Request;

class FiscalYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:fiscal-year-manage');
    }

    public function index(Request $request)
    {
        $query = FiscalYear::query()->orderByDesc('start_date');

        if ($request->filled('query')) {
            $query->where('name', 'like', '%' . $request->input('query') . '%');
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        if ($request->filled('is_closed')) {
            $query->where('is_closed', $request->boolean('is_closed'));
        }

        $years = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.finance.fiscal-years.partials.table-rows', compact('years'))->render(),
                'pagination' => view('admin.pages.finance.fiscal-years.partials.pagination', compact('years'))->render(),
            ]);
        }

        return view('admin.pages.finance.fiscal-years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.pages.finance.fiscal-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        FiscalYear::create([
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => true,
            'is_closed' => false,
        ]);

        return redirect()->route('admin.fiscal-years.index')
            ->with('success', 'تم إضافة سنة مالية جديدة.');
    }

    public function close(FiscalYear $fiscalYear, \App\Services\Accounting\AccountingService $accounting)
    {
        if ($fiscalYear->is_closed) {
            return back()->with('error', 'السنة المالية مغلقة مسبقاً.');
        }

        $accounting->createClosingEntries($fiscalYear);

        $fiscalYear->update(['is_closed' => true, 'is_active' => false]);

        return redirect()->route('admin.fiscal-years.index')
            ->with('success', 'تم إقفال السنة المالية وإنشاء قيود الإقفال.');
    }
}
