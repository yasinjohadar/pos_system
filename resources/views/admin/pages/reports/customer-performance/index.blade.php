@extends('admin.layouts.master')

@section('page-title')
    تقرير أداء العملاء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto"><h5 class="page-title fs-21 mb-1">تقرير أداء العملاء</h5></div>
            <div>
                <a href="{{ route('admin.reports.customer-performance.top') }}" class="btn btn-outline-primary btn-sm me-1">أفضل العملاء</a>
                <a href="{{ route('admin.reports.customer-performance.inactive') }}" class="btn btn-outline-secondary btn-sm">عملاء غير نشطين</a>
            </div>
        </div>
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3"><label class="form-label">من تاريخ</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
                    <div class="col-md-3"><label class="form-label">إلى تاريخ</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
                    <div class="col-md-2"><button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button></div>
                </form>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr><th>العميل</th><th class="text-end">عدد الفواتير</th><th class="text-end">إجمالي المبيعات</th><th class="text-end">متوسط الفاتورة</th><th>آخر شراء</th></tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row->customer_name }}</td>
                                <td class="text-end">{{ $row->invoice_count }}</td>
                                <td class="text-end">{{ number_format($row->total_sales, 2) }}</td>
                                <td class="text-end">{{ number_format($row->avg_invoice_value, 2) }}</td>
                                <td>{{ $row->last_invoice_date ? \Carbon\Carbon::parse($row->last_invoice_date)->format('Y-m-d') : '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center">لا توجد بيانات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
