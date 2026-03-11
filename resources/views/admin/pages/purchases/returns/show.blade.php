@extends('admin.layouts.master')

@section('page-title')
    مرتجع الشراء {{ $purchaseReturn->return_number }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">مرتجع الشراء: {{ $purchaseReturn->return_number }}</h5>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if($purchaseReturn->status === \App\Models\PurchaseReturn::STATUS_PENDING)
                    @can('purchase-return-complete')
                    <form action="{{ route('admin.purchase-returns.complete', $purchaseReturn) }}" method="POST" class="d-inline" onsubmit="return confirm('إكمال المرتجع س يصرف الكميات من المخزون. متابعة؟');">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i> إكمال المرتجع</button>
                    </form>
                    @endcan
                @endif
                <a href="{{ route('admin.purchase-returns.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-right me-1"></i> رجوع</a>
                <a href="{{ route('admin.purchase-invoices.show', $purchaseReturn->purchase_invoice_id) }}" class="btn btn-outline-info btn-sm">الفاتورة الأصلية</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">بيانات المرتجع</h6></div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted">الفاتورة الأصلية:</td><td><a href="{{ route('admin.purchase-invoices.show', $purchaseReturn->purchaseInvoice) }}">{{ $purchaseReturn->purchaseInvoice->number ?? '—' }}</a></td></tr>
                            <tr><td class="text-muted">مخزن الصرف:</td><td>{{ $purchaseReturn->warehouse->name ?? '—' }}</td></tr>
                            <tr><td class="text-muted">تاريخ المرتجع:</td><td>{{ $purchaseReturn->return_date->format('Y-m-d') }}</td></tr>
                            <tr><td class="text-muted">الحالة:</td>
                                <td>
                                    @if($purchaseReturn->status === 'pending')
                                        <span class="badge bg-warning">قيد الانتظار</span>
                                    @elseif($purchaseReturn->status === 'completed')
                                        <span class="badge bg-success">مكتمل</span>
                                    @else
                                        <span class="badge bg-danger">ملغى</span>
                                    @endif
                                </td>
                            </tr>
                            <tr><td class="text-muted">المستخدم:</td><td>{{ $purchaseReturn->user->name ?? '—' }}</td></tr>
                            @if($purchaseReturn->notes)
                            <tr><td class="text-muted">ملاحظات:</td><td>{{ $purchaseReturn->notes }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">بنود المرتجع</h6></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>المنتج</th>
                                        <th>الكمية</th>
                                        <th>سعر الوحدة</th>
                                        <th>الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseReturn->items as $item)
                                        <tr>
                                            <td>{{ $item->product->name ?? '—' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }}</td>
                                            <td>{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">الإجماليات</h6></div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td>المجموع المرتجع:</td><td class="text-end">{{ number_format($purchaseReturn->subtotal_refund, 2) }}</td></tr>
                            <tr><td>الضريبة:</td><td class="text-end">{{ number_format($purchaseReturn->tax_refund, 2) }}</td></tr>
                            <tr><th>الإجمالي المرتجع:</th><td class="text-end"><strong>{{ number_format($purchaseReturn->total_refund, 2) }}</strong></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
