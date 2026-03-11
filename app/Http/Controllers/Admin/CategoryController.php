<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:category-list')->only('index');
        $this->middleware('permission:category-create')->only(['create', 'store']);
        $this->middleware('permission:category-edit')->only(['edit', 'update']);
        $this->middleware('permission:category-delete')->only('destroy');
        $this->middleware('permission:category-show')->only('show');
    }

    public function index(Request $request)
    {
        $query = Category::with('parent')->withCount('products')->orderBy('order')->orderBy('name');

        if ($request->filled('query')) {
            $search = $request->input('query');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('slug', 'like', "%$search%");
            });
        }

        if ($request->filled('parent_id')) {
            if ($request->input('parent_id') === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->input('parent_id'));
            }
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $categories = $query->paginate(15);
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.pages.categories.index', compact('categories', 'parentCategories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.pages.categories.create', compact('parentCategories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function show(Category $category)
    {
        $category->load(['parent', 'children', 'products']);
        return view('admin.pages.categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.pages.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف بنجاح');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'لا يمكن حذف التصنيف لوجود تصنيفات فرعية.');
        }

        if ($category->products()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'لا يمكن حذف التصنيف لوجود منتجات مرتبطة به.');
        }

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف بنجاح');
    }
}
