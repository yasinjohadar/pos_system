<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;

class ChartOfAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:chart-of-account-list')->only('index');
        $this->middleware('permission:chart-of-account-create')->only(['create', 'store']);
        $this->middleware('permission:chart-of-account-edit')->only(['edit', 'update']);
        $this->middleware('permission:chart-of-account-delete')->only('destroy');
    }

    public function index()
    {
        $accounts = ChartOfAccount::withCount('journalEntryLines')
            ->orderBy('code')
            ->paginate(20);
        return view('admin.pages.chart-of-accounts.index', compact('accounts'));
    }

    public function create()
    {
        $parents = ChartOfAccount::orderBy('code')->get(['id', 'code', 'name']);
        return view('admin.pages.chart-of-accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:chart_of_accounts,code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'sometimes|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['level'] = 1;
        if (!empty($validated['parent_id'])) {
            $parent = ChartOfAccount::find($validated['parent_id']);
            $validated['level'] = ($parent->level ?? 1) + 1;
        }
        ChartOfAccount::create($validated);
        return redirect()->route('admin.chart-of-accounts.index')->with('success', 'تم إضافة الحساب بنجاح.');
    }

    public function edit(ChartOfAccount $chartOfAccount)
    {
        $parents = ChartOfAccount::where('id', '!=', $chartOfAccount->id)->orderBy('code')->get(['id', 'code', 'name']);
        return view('admin.pages.chart-of-accounts.edit', compact('chartOfAccount', 'parents'));
    }

    public function update(Request $request, ChartOfAccount $chartOfAccount)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:chart_of_accounts,code,' . $chartOfAccount->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:chart_of_accounts,id',
            'is_active' => 'sometimes|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $chartOfAccount->update($validated);
        return redirect()->route('admin.chart-of-accounts.index')->with('success', 'تم تحديث الحساب بنجاح.');
    }

    public function destroy(ChartOfAccount $chartOfAccount)
    {
        if ($chartOfAccount->journalEntryLines()->exists()) {
            return redirect()->route('admin.chart-of-accounts.index')
                ->with('error', 'لا يمكن حذف حساب له حركات في القيود.');
        }
        $chartOfAccount->delete();
        return redirect()->route('admin.chart-of-accounts.index')->with('success', 'تم حذف الحساب.');
    }
}
