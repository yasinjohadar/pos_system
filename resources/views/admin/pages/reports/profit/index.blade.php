@extends('admin.layouts.master')

@section('page-title')
    تقرير الأرباح
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير الأرباح</h5>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="from_date" class="form-control" value="{{ $from->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="to_date" class="form-control" value="{{ $to->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">إجمالي المبيعات (بعد المرتجعات)</h6>
                        <h3 class="mb-0">{{ number_format($summary['sales_total'] - $summary['sales_returns'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">إجمالي المشتريات (بعد المرتجعات)</h6>
                        <h3 class="mb-0">{{ number_format($summary['purchases_total'] - $summary['purchase_returns'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">إيرادات أخرى (سندات قبض)</h6>
                        <h3 class="mb-0">{{ number_format($summary['vouchers_receipts'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">مصروفات أخرى (سندات صرف)</h6>
                        <h3 class="mb-0">{{ number_format($summary['vouchers_payments'], 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">الربح الإجمالي التقديري</h6>
                        <h3 class="mb-0">{{ number_format($summary['gross_profit'], 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

