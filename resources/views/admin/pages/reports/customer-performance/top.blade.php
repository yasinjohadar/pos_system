@extends('admin.layouts.master')

@section('page-title')
    أفضل العملاء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4">
            <h5 class="page-title fs-21 mb-1">أفضل العملاء</h5>
            <a href="{{ route('admin.reports.customer-performance.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form method="GET" class="row g-3 mb-3">
                    <div class="col-md-2"><label class="form-label">من</label><input type="date" name="from" class="form-control" value="{{ $from }}"></div>
                    <div class="col-md-2"><label class="form-label">إلى</label><input type="date" name="to" class="form-control" value="{{ $to }}"></div>
                    <div class="col-md-2"><label class="form-label">عدد</label><input type="number" name="limit" class="form-control" value="{{ $limit }}" min="5" max="50"></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary">عرض</button></div>
                </form>
                <table class="table table-striped table-bordered mb-0">
                    <thead><tr><th>#</th><th>العميل</th><th class="text-end">عدد الفواتير</th><th class="text-end">إجمالي المبيعات</th></tr></thead>
                    <tbody>
                        @forelse($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->customer_name }}</td>
                                <td class="text-end">{{ $row->invoice_count }}</td>
                                <td class="text-end">{{ number_format($row->total_sales, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">لا توجد بيانات.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
