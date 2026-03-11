@extends('admin.layouts.master')

@section('page-title')
    تقرير أعمار ديون العملاء
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تقرير أعمار ديون العملاء</h5>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">العميل</label>
                        <select name="customer_id" class="form-select" required>
                            <option value="">اختر العميل</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ (int)$selectedCustomerId === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">حتى تاريخ</label>
                        <input type="date" name="as_of_date" class="form-control" value="{{ $asOfDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button>
                    </div>
                </form>
            </div>
        </div>

        @if($aging)
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="mb-3">توزيع الرصيد حسب العمر</h6>
                <table class="table table-bordered text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>0-30 يوم</th>
                            <th>31-60 يوم</th>
                            <th>61-90 يوم</th>
                            <th>أكثر من 90 يوم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ number_format($aging['0_30'], 2) }}</td>
                            <td>{{ number_format($aging['31_60'], 2) }}</td>
                            <td>{{ number_format($aging['61_90'], 2) }}</td>
                            <td>{{ number_format($aging['90_plus'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

