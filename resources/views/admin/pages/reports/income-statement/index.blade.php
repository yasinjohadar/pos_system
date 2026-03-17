@extends('admin.layouts.master')

@section('page-title')
    قائمة الدخل
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto"><h5 class="page-title fs-21 mb-1">قائمة الدخل</h5></div>
            <div>
                <a href="{{ route('admin.reports.income-statement.index', array_merge(request()->only(['from', 'to']), ['format' => 'csv'])) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-csv me-1"></i> تصدير CSV</a>
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
            <div class="card-body">
                <table class="table table-borderless mb-0" style="max-width: 400px;">
                    <tr>
                        <td class="text-muted">الإيرادات (حسابات إيرادات):</td>
                        <td class="text-end"><strong>{{ number_format($revenue, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">المصروفات (حسابات مصروفات):</td>
                        <td class="text-end"><strong>- {{ number_format($expense, 2) }}</strong></td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>صافي الدخل:</strong></td>
                        <td class="text-end"><strong>{{ number_format($netIncome, 2) }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
