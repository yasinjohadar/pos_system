@extends('admin.layouts.master')

@section('page-title')
    أفضل المنتجات مبيعاً
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4">
            <h5 class="page-title fs-21 mb-1">أفضل المنتجات مبيعاً</h5>
            <a href="{{ route('admin.reports.product-performance.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
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
                    <thead><tr><th>#</th><th>المنتج</th><th class="text-end">الكمية</th><th class="text-end">الإيرادات</th><th class="text-end">الربح</th></tr></thead>
                    <tbody>
                        @forelse($rows as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->product_name }}</td>
                                <td class="text-end">{{ number_format($row->total_qty, 2) }}</td>
                                <td class="text-end">{{ number_format($row->total_revenue, 2) }}</td>
                                <td class="text-end">{{ number_format($row->profit, 2) }}</td>
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
