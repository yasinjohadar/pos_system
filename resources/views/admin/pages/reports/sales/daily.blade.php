@extends('admin.layouts.master')

@section('page-title')
    تقرير المبيعات اليومي
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير المبيعات اليومي</h5>
            </div>
            <div>
                <a href="{{ route('admin.reports.sales.daily', array_merge(request()->only(['date', 'branch_id']), ['format' => 'csv'])) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-csv me-1"></i> تصدير CSV</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">عدد الفواتير المؤكدة</h6>
                        <h3 class="mb-0">{{ $summary['invoices_count'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">إجمالي المبيعات</h6>
                        <h3 class="mb-0">{{ number_format($summary['total_sales'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">صافي المبيعات</h6>
                        <h3 class="mb-0">{{ number_format($summary['net_sales'], 2) }}</h3>
                        <small class="text-muted d-block mt-1">مرتجعات: {{ number_format($summary['total_returns'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

