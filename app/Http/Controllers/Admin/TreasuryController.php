<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Treasury;
use Illuminate\Http\Request;

class TreasuryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:treasury-list')->only('index');
        $this->middleware('permission:treasury-create')->only(['create', 'store']);
        $this->middleware('permission:treasury-edit')->only(['edit', 'update']);
        $this->middleware('permission:treasury-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Treasury::query()->with('branch')->orderBy('type')->orderBy('name');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $treasuries = $query->paginate(15);

        return view('admin.pages.sales.treasuries.index', compact('treasuries'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.sales.treasuries.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cashbox,bank',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        Treasury::create($validated);

        return redirect()->route('admin.treasuries.index')
            ->with('success', 'تم إضافة الخزنة/البنك بنجاح');
    }

    public function edit(Treasury $treasury)
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.sales.treasuries.edit', compact('treasury', 'branches'));
    }

    public function update(Request $request, Treasury $treasury)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cashbox,bank',
            'branch_id' => 'nullable|exists:branches,id',
            'opening_balance' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;

        $treasury->update($validated);

        return redirect()->route('admin.treasuries.index')
            ->with('success', 'تم تحديث الخزنة/البنك بنجاح');
    }

    public function destroy(Treasury $treasury)
    {
        if ($treasury->salePayments()->exists() || $treasury->supplierPayments()->exists()) {
            return redirect()->route('admin.treasuries.index')
                ->with('error', 'لا يمكن حذف الخزنة/البنك لأنه مرتبط بدفعات.');
        }

        $treasury->delete();

        return redirect()->route('admin.treasuries.index')
            ->with('success', 'تم حذف الخزنة/البنك بنجاح');
    }
}
