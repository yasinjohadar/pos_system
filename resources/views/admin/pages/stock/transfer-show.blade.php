@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التحويل
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تحويل #{{ $transfer->id }}</h5>
            </div>
            <div>
                <a href="{{ route('admin.stock.transfers.index') }}" class="btn btn-secondary btn-sm">رجوع</a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="140">التاريخ:</td><td>{{ $transfer->transfer_date->format('Y-m-d') }}</td></tr>
                            <tr><td class="text-muted">من مخزن:</td><td>{{ $transfer->fromWarehouse->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">إلى مخزن:</td><td>{{ $transfer->toWarehouse->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">الحالة:</td><td><span class="badge bg-success">{{ $transfer->status }}</span></td></tr>
                            <tr><td class="text-muted">المستخدم:</td><td>{{ $transfer->user->name ?? '—' }}</td></tr>
                            @if($transfer->notes)
                                <tr><td class="text-muted">ملاحظات:</td><td>{{ $transfer->notes }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header"><h6 class="mb-0">البنود</h6></div>
                    <div class="card-body">
                        @php
                            $items = $transfer->movements->where('type', 'transfer_out')->groupBy('product_id');
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $productId => $movements)
                                        <tr>
                                            <td>{{ $movements->first()->product->name ?? $productId }}</td>
                                            <td>{{ number_format(abs($movements->sum('quantity')), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
