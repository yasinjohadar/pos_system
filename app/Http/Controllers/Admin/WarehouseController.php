<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:warehouse-list')->only('index');
        $this->middleware('permission:warehouse-create')->only(['create', 'store']);
        $this->middleware('permission:warehouse-edit')->only(['edit', 'update']);
        $this->middleware('permission:warehouse-delete')->only('destroy');
        $this->middleware('permission:warehouse-show')->only('show');
    }

    /**
     * Display a listing of warehouses (optionally filtered by branch).
     */
    public function index(Request $request)
    {
        $query = Warehouse::with('branch')->orderBy('name');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $warehouses = $query->paginate(15);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.warehouses.index', compact('warehouses', 'branches'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create(Request $request)
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $selectedBranchId = $request->old('branch_id', $request->query('branch_id'));

        return view('admin.pages.warehouses.create', compact('branches', 'selectedBranchId'));
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_default']) {
            Warehouse::where('branch_id', $validated['branch_id'])->update(['is_default' => false]);
        }

        Warehouse::create($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'تم إضافة المخزن بنجاح');
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load('branch');

        return view('admin.pages.warehouses.show', compact('warehouse'));
    }

    /**
     * Show the form for editing the specified warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('admin.pages.warehouses.edit', compact('warehouse', 'branches'));
    }

    /**
     * Update the specified warehouse.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_default'] = $request->boolean('is_default', false);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($validated['is_default']) {
            Warehouse::where('branch_id', $validated['branch_id'])
                ->where('id', '!=', $warehouse->id)
                ->update(['is_default' => false]);
        }

        $warehouse->update($validated);

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'تم تحديث المخزن بنجاح');
    }

    /**
     * Remove the specified warehouse.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'تم حذف المخزن بنجاح');
    }
}
