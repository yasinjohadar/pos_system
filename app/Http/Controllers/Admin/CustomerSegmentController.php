<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerSegment;
use Illuminate\Http\Request;

class CustomerSegmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-segment-list')->only('index');
        $this->middleware('permission:customer-segment-create')->only(['create', 'store']);
        $this->middleware('permission:customer-segment-edit')->only(['edit', 'update']);
        $this->middleware('permission:customer-segment-delete')->only('destroy');
    }

    public function index()
    {
        $segments = CustomerSegment::withCount('customers')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.pages.sales.customer-segments.index', compact('segments'));
    }

    public function create()
    {
        return view('admin.pages.sales.customer-segments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'sometimes|boolean',
        ]);

        CustomerSegment::create([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#6366f1',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.customer-segments.index')
            ->with('success', 'تم إنشاء شريحة العملاء بنجاح.');
    }

    public function edit(CustomerSegment $customerSegment)
    {
        return view('admin.pages.sales.customer-segments.edit', compact('customerSegment'));
    }

    public function update(Request $request, CustomerSegment $customerSegment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'sometimes|boolean',
        ]);

        $customerSegment->update([
            'name' => $validated['name'],
            'name_en' => $validated['name_en'] ?? null,
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#6366f1',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.customer-segments.index')
            ->with('success', 'تم تحديث شريحة العملاء بنجاح.');
    }

    public function destroy(CustomerSegment $customerSegment)
    {
        if ($customerSegment->customers()->exists()) {
            return redirect()->route('admin.customer-segments.index')
                ->with('error', 'لا يمكن حذف شريحة مرتبطة بعملاء. قم بإزالة الشريحة من العملاء أولاً.');
        }

        $customerSegment->delete();

        return redirect()->route('admin.customer-segments.index')
            ->with('success', 'تم حذف شريحة العملاء بنجاح.');
    }
}
