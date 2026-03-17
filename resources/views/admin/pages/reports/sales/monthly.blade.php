@extends('admin.layouts.master')

@section('page-title')
    تقرير المبيعات الشهري
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير المبيعات الشهري</h5>
            </div>
            <div>
                <a href="{{ route('admin.reports.sales.monthly', array_merge(request()->only(['year', 'month', 'branch_id']), ['format' => 'csv'])) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-csv me-1"></i> تصدير CSV</a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">السنة</label>
                        <input type="number" name="year" class="form-control" value="{{ $year }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الشهر</label>
                        <input type="number" name="month" class="form-control" value="{{ $month }}" min="1" max="12">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header">
                <h6 class="mb-0">إجمالي المبيعات اليومية</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th class="text-end">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chart['labels'] as $i => $label)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="text-end">{{ number_format($chart['totals'][$i], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

