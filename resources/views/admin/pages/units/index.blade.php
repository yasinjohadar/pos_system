@extends('admin.layouts.master')

@section('page-title')
    الوحدات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">الوحدات</h5>
            </div>
            @can('unit-create')
            <div>
                <a href="{{ route('admin.units.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> إضافة وحدة
                </a>
            </div>
            @endcan
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-4">
                                <input type="text" name="query" class="form-control" placeholder="بحث بالاسم أو الرمز" value="{{ request('query') }}">
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
                                        <th>الرمز</th>
                                        <th>الوحدة الأساسية</th>
                                        <th>معامل التحويل</th>
                                        <th>عدد المنتجات</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($units as $unit)
                                        <tr>
                                            <td>{{ $unit->id }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td>{{ $unit->symbol ?? '—' }}</td>
                                            <td>{{ $unit->baseUnit->name ?? '—' }}</td>
                                            <td>{{ $unit->conversion_factor }}</td>
                                            <td>{{ $unit->products_count }}</td>
                                            <td>
                                                @if($unit->is_active)
                                                    <span class="badge bg-success">نشط</span>
                                                @else
                                                    <span class="badge bg-danger">غير نشط</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                    @can('unit-show')
                                                    <a href="{{ route('admin.units.show', $unit) }}" class="btn btn-sm btn-info" title="عرض"><i class="fas fa-eye"></i></a>
                                                    @endcan
                                                    @can('unit-edit')
                                                    <a href="{{ route('admin.units.edit', $unit) }}" class="btn btn-sm btn-primary" title="تعديل"><i class="fas fa-edit"></i></a>
                                                    @endcan
                                                    @can('unit-delete')
                                                    <form action="{{ route('admin.units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟');">
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
                                            <td colspan="8" class="text-center">لا توجد وحدات.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($units->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $units->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
