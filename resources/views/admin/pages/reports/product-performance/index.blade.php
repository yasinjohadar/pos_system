@extends('admin.layouts.master')

@section('page-title')
    تقرير أداء المنتجات
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير أداء المنتجات</h5>
            </div>
            <div>
                <a href="{{ route('admin.reports.product-performance.top') }}" class="btn btn-outline-primary btn-sm me-1">أفضل المنتجات</a>
                <a href="{{ route('admin.reports.product-performance.no-sales') }}" class="btn btn-outline-secondary btn-sm">منتجات بدون مبيعات</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from" class="form-control" value="{{ $from }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to" class="form-control" value="{{ $to }}">
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
                                <th class="text-end">الكمية</th>
                                <th class="text-end">الإيرادات</th>
                                <th class="text-end">الربح</th>
                                <th class="text-end">الهامش %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $row)
                                <tr>
                                    <td>{{ $row->product_name }}</td>
                                    <td>{{ $row->category_name }}</td>
                                    <td class="text-end">{{ number_format($row->total_qty, 2) }}</td>
                                    <td class="text-end">{{ number_format($row->total_revenue, 2) }}</td>
                                    <td class="text-end">{{ number_format($row->profit, 2) }}</td>
                                    <td class="text-end">{{ $row->margin_percent }}%</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">لا توجد بيانات في الفترة المحددة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
