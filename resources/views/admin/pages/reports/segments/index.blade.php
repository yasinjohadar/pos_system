@extends('admin.layouts.master')

@section('page-title')
    تقرير الشرائح
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير الشرائح</h5>
            </div>
            <div>
                <a href="{{ route('admin.reports.segments.index', ['format' => 'csv']) }}" class="btn btn-outline-success btn-sm me-1"><i class="fas fa-file-csv me-1"></i> تصدير CSV</a>
                <a href="{{ route('admin.customer-segments.index') }}" class="btn btn-outline-primary btn-sm">إدارة الشرائح</a>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>الشريحة</th>
                            <th class="text-end">عدد العملاء</th>
                            <th class="text-end">إجمالي المبيعات</th>
                            <th class="text-end">متوسط الرصيد</th>
                            <th class="text-end">عدد الفواتير</th>
                            <th class="text-end">متوسط قيمة الفاتورة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                <td>{{ $row->segment_name }}</td>
                                <td class="text-end">{{ $row->customer_count }}</td>
                                <td class="text-end">{{ number_format($row->total_sales, 2) }}</td>
                                <td class="text-end">{{ number_format($row->avg_balance, 2) }}</td>
                                <td class="text-end">{{ $row->invoice_count }}</td>
                                <td class="text-end">{{ number_format($row->avg_invoice_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">لا توجد شرائح نشطة أو لا توجد بيانات.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
