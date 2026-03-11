@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المخزن
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تفاصيل المخزن: {{ $warehouse->name }}</h5>
            </div>
            <div class="d-flex gap-2">
                @can('warehouse-edit')
                <a href="{{ route('admin.warehouses.edit', $warehouse) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> تعديل المخزن
                </a>
                @endcan
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header">
                        <h6 class="mb-0">بيانات المخزن</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted" width="140">الاسم:</td>
                                <td>{{ $warehouse->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">الكود:</td>
                                <td>{{ $warehouse->code ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">الفرع:</td>
                                <td>
                                    <a href="{{ route('admin.branches.show', $warehouse->branch) }}">{{ $warehouse->branch->name }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">العنوان:</td>
                                <td>{{ $warehouse->address ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">افتراضي:</td>
                                <td>
                                    @if($warehouse->is_default)
                                        <span class="badge bg-primary">نعم</span>
                                    @else
                                        لا
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">الحالة:</td>
                                <td>
                                    @if($warehouse->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
