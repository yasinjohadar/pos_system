<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:tax-list')->only('index');
        $this->middleware('permission:tax-create')->only(['create', 'store']);
        $this->middleware('permission:tax-edit')->only(['edit', 'update']);
        $this->middleware('permission:tax-delete')->only('destroy');
    }

    public function index(Request $request)
    {
        $query = Tax::query()->orderBy('name');
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }
        $taxes = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.taxes.partials.table-rows', compact('taxes'))->render(),
                'pagination' => view('admin.pages.taxes.partials.pagination', compact('taxes'))->render(),
            ]);
        }

        return view('admin.pages.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.pages.taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,fixed',
            'rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        Tax::create($validated);

        return redirect()->route('admin.taxes.index')->with('success', 'تم إضافة الضريبة.');
    }

    public function edit(Tax $tax)
    {
        return view('admin.pages.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percent,fixed',
            'rate' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $tax->update($validated);

        return redirect()->route('admin.taxes.index')->with('success', 'تم تحديث الضريبة.');
    }

    public function destroy(Tax $tax)
    {
        $tax->delete();
        return redirect()->route('admin.taxes.index')->with('success', 'تم حذف الضريبة.');
    }
}
