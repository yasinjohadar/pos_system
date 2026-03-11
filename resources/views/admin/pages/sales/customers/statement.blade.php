@extends('admin.layouts.master')

@section('page-title')
    كشف حساب العميل: {{ $customer->name }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">كشف حساب العميل: {{ $customer->name }}</h5>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">رصيد حتى تاريخ (اختياري)</label>
                        <input type="date" name="as_of_date" class="form-control" value="{{ $asOfDate?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> عرض</button>
                    </div>
                    @if($asOfDate)
                    <div class="col-md-2">
                        <a href="{{ route('admin.customers.statement', $customer) }}" class="btn btn-outline-secondary">عرض الكل</a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">حركات الحساب</h6>
                <span class="badge bg-secondary">رصيد افتتاحي: {{ number_format($customer->opening_balance, 2) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>المرجع</th>
                                <th>البيان</th>
                                <th class="text-end">مدين</th>
                                <th class="text-end">دائن</th>
                                <th class="text-end">الرصيد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entries as $e)
                                <tr>
                                    <td>{{ $e['date']->format('Y-m-d') }}</td>
                                    <td>
                                        @if($e['type'] === 'invoice')
                                            <span class="badge bg-primary">فاتورة</span>
                                        @elseif($e['type'] === 'return')
                                            <span class="badge bg-warning">مرتجع</span>
                                        @else
                                            <span class="badge bg-success">دفعة</span>
                                        @endif
                                    </td>
                                    <td>{{ $e['reference'] }}</td>
                                    <td>{{ $e['description'] }}</td>
                                    <td class="text-end">{{ $e['debit'] > 0 ? number_format($e['debit'], 2) : '—' }}</td>
                                    <td class="text-end">{{ $e['credit'] > 0 ? number_format($e['credit'], 2) : '—' }}</td>
                                    <td class="text-end"><strong>{{ number_format($e['balance'], 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">لا توجد حركات لهذا العميل{{ $asOfDate ? ' حتى التاريخ المحدد' : '' }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($entries->isNotEmpty())
                <div class="card-footer text-end">
                    <strong>الرصيد الحالي:</strong> {{ number_format($entries->last()['balance'], 2) }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@stop
