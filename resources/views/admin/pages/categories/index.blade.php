@extends('admin.layouts.master')

@section('page-title')
    التصنيفات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">التصنيفات</h5>
            </div>
            @can('category-create')
            <div>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> إضافة تصنيف
                </a>
            </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-3">
                                <input type="text" name="query" class="form-control" placeholder="بحث بالاسم أو الرابط" value="{{ request('query') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="parent_id" class="form-select">
                                    <option value="">جميع التصنيفات</option>
                                    <option value="null" {{ request('parent_id') === 'null' ? 'selected' : '' }}>أساسية فقط</option>
                                    @foreach($parentCategories as $c)
                                        <option value="{{ $c->id }}" {{ request('parent_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-select">
                                    <option value="">الكل</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search me-1"></i> بحث</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>التصنيف الأب</th>
                                        <th>عدد المنتجات</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->parent->name ?? '—' }}</td>
                                            <td>{{ $category->products_count }}</td>
                                            <td>
                                                @if($category->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                    @can('category-show')
                                                    <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-info" title="عرض"><i class="fas fa-eye"></i></a>
                                                    @endcan
                                                    @can('category-edit')
                                                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                                    @endcan
                                                    @can('category-delete')
                                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التصنيف؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">لا توجد تصنيفات.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($categories->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $categories->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
