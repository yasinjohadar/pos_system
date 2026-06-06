<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\MergesPhoneInput;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    use MergesPhoneInput;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:branch-list')->only('index');
        $this->middleware('permission:branch-create')->only(['create', 'store']);
        $this->middleware('permission:branch-edit')->only(['edit', 'update', 'toggleStatus']);
        $this->middleware('permission:branch-delete')->only('destroy');
        $this->middleware('permission:branch-show')->only('show');
    }

    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $branches = $this->buildBranchesQuery($request)
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'tbody' => view('admin.pages.branches.partials.table-rows', compact('branches'))->render(),
                'pagination' => view('admin.pages.branches.partials.pagination', compact('branches'))->render(),
            ]);
        }

        return view('admin.pages.branches.index', compact('branches'));
    }

    private function buildBranchesQuery(Request $request)
    {
        $query = Branch::withCount('warehouses')->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (int) $request->input('is_active'));
        }

        return $query;
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
        $validated = $this->validateRequestWithPhone($request, array_merge([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ], $this->phoneRules('phone')), [
            'phone.regex' => 'رقم الهاتف غير صحيح — أدخل الرقم بدون صفر في البداية',
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
        $validated = $this->validateRequestWithPhone($request, array_merge([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $branch->id,
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ], $this->phoneRules('phone')), [
            'phone.regex' => 'رقم الهاتف غير صحيح — أدخل الرقم بدون صفر في البداية',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')
            ->with('success', 'تم تحديث الفرع بنجاح');
    }

    /**
     * Toggle branch active status via AJAX.
     */
    public function toggleStatus(Request $request, Branch $branch)
    {
        try {
            $branch->update(['is_active' => ! $branch->is_active]);
            $branch->refresh();

            $status = $branch->is_active ? 'نشط' : 'غير نشط';

            return response()->json([
                'success' => true,
                'message' => "تم تحديث حالة الفرع إلى: {$status}",
                'is_active' => (bool) $branch->is_active,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error toggling branch status', [
                'branch_id' => $branch->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة الفرع',
            ], 500);
        }
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
