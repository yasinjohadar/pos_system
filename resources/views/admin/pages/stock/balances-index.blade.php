@extends('admin.layouts.master')

@section('page-title')
    أرصدة المخزون
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">أرصدة المخزون</h5>
            </div>
            <div>
                <a href="{{ route('admin.stock.movements.index') }}" class="btn btn-outline-primary btn-sm">حركات المخزون</a>
                <a href="{{ route('admin.stock.balances.index', ['low_stock' => 1]) }}" class="btn btn-warning btn-sm">تنبيهات انخفاض المخزون</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="GET" class="mb-4 row g-3">
                            <div class="col-md-3">
                                <select name="warehouse_id" class="form-select">
                                    <option value="">جميع المخازن</option>
                                    @foreach($warehouses as $w)
                                        <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="low_stock" {{ request('low_stock') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_stock">تنبيه انخفاض فقط</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100"><i class="fas fa-search me-1"></i> بحث</button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered text-center mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>المنتج</th>
                                        <th>المخزن</th>
                                        <th>الرصيد</th>
                                        <th>حد التنبيه</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($balances as $b)
                                        @php
                                            $isLow = $b->product && $b->product->min_stock_alert > 0 && (float) $b->quantity <= (float) $b->product->min_stock_alert;
                                        @endphp
                                        <tr class="{{ $isLow ? 'table-warning' : '' }}">
                                            <td>{{ $b->id }}</td>
                                            <td>{{ $b->product->name ?? '—' }}</td>
                                            <td>{{ $b->warehouse->name ?? '—' }}</td>
                                            <td>{{ number_format($b->quantity, 2) }}</td>
                                            <td>{{ $b->product ? $b->product->min_stock_alert : '—' }}</td>
                                            <td>
                                                @if($isLow)
                                                    <span class="badge bg-warning text-dark">انخفاض</span>
                                                @else
                                                    <span class="badge bg-success">طبيعي</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">لا توجد أرصدة أو لا توجد نتائج.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($balances->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $balances->withQueryString()->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
