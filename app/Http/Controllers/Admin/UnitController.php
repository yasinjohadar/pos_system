<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:unit-list')->only('index');
        $this->middleware('permission:unit-create')->only(['create', 'store']);
        $this->middleware('permission:unit-edit')->only(['edit', 'update']);
        $this->middleware('permission:unit-delete')->only('destroy');
        $this->middleware('permission:unit-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Unit::with('baseUnit')->withCount('products')->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('symbol', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $units = $query->paginate(15);

        return view('admin.pages.units.index', compact('units'));
    }

    public function create()
    {
        $baseUnits = Unit::whereNull('base_unit_id')->where('is_active', true)->orderBy('name')->get();
        return view('admin.pages.units.create', compact('baseUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:20',
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'is_active' => 'boolean',
        ]);

        $validated['conversion_factor'] = $validated['conversion_factor'] ?? 1;
        $validated['is_active'] = $request->boolean('is_active', true);

        Unit::create($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'تم إضافة الوحدة بنجاح');
    }

    public function show(Unit $unit)
    {
        $unit->load(['baseUnit', 'subUnits', 'products']);
        return view('admin.pages.units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        $baseUnits = Unit::whereNull('base_unit_id')->where('is_active', true)->where('id', '!=', $unit->id)->orderBy('name')->get();
        return view('admin.pages.units.edit', compact('unit', 'baseUnits'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:20',
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'is_active' => 'boolean',
        ]);

        $validated['conversion_factor'] = $validated['conversion_factor'] ?? 1;
        $validated['is_active'] = $request->boolean('is_active', true);

        $unit->update($validated);

        return redirect()->route('admin.units.index')
            ->with('success', 'تم تحديث الوحدة بنجاح');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->products()->exists()) {
            return redirect()->route('admin.units.index')
                ->with('error', 'لا يمكن حذف الوحدة لوجود منتجات مرتبطة بها.');
        }

        $unit->delete();

        return redirect()->route('admin.units.index')
            ->with('success', 'تم حذف الوحدة بنجاح');
    }
}
