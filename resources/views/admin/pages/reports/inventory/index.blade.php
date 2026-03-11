@extends('admin.layouts.master')

@section('page-title')
    تقرير المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير المخزون الحالي</h5>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">المخزن</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">جميع المخازن</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ (int)$warehouseId === $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">التصنيف</label>
                        <select name="category_id" class="form-select">
                            <option value="">جميع التصنيفات</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (int)$categoryId === $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>المنتج</th>
                                <th>التصنيف</th>
                                <th>المخزن</th>
                                <th class="text-end">الكمية</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td>{{ $row->product->name ?? '—' }}</td>
                                    <td>{{ $row->product->category->name ?? '—' }}</td>
                                    <td>{{ $row->warehouse->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($row->quantity, 4) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد بيانات.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

