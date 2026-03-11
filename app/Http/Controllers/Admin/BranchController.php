<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:branch-list')->only('index');
        $this->middleware('permission:branch-create')->only(['create', 'store']);
        $this->middleware('permission:branch-edit')->only(['edit', 'update']);
        $this->middleware('permission:branch-delete')->only('destroy');
        $this->middleware('permission:branch-show')->only('show');
    }

    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $query = Branch::withCount('warehouses')->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $branches = $query->paginate(15);

        return view('admin.pages.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('admin.pages.branches.create');
    }

    /**
     * Store a newly created branch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'تم إضافة الفرع بنجاح');
    }

    /**
     * Display the specified branch with its warehouses.
     */
    public function show(Branch $branch)
    {
        $branch->load('warehouses');

        return view('admin.pages.branches.show', compact('branch'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        return view('admin.pages.branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $branch->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'تم تحديث الفرع بنجاح');
    }

    /**
     * Remove the specified branch.
     */
    public function destroy(Branch $branch)
    {
        if ($branch->warehouses()->exists()) {
            return redirect()->route('admin.branches.index')
                ->with('error', 'لا يمكن حذف الفرع لأنه مرتبط بمخازن. احذف أو انقل المخازن أولاً.');
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')
            ->with('success', 'تم حذف الفرع بنجاح');
    }
}
