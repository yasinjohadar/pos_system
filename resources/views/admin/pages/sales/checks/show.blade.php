@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الشيك: {{ $check->check_number }}
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div class="my-auto">
                <h5 class="page-title fs-21 mb-1">تفاصيل الشيك: {{ $check->check_number }}</h5>
            </div>
            <div class="d-flex gap-2">
                @can('check-edit')
                @if($check->status === \App\Models\Check::STATUS_UNDER_COLLECTION)
                    <form action="{{ route('admin.checks.update-status', $check) }}" method="POST" class="d-inline" onsubmit="return confirm('تحديث الحالة إلى محصل؟');">
                        @csrf
                        <input type="hidden" name="status" value="collected">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i> محصل</button>
                    </form>
                    <form action="{{ route('admin.checks.update-status', $check) }}" method="POST" class="d-inline" onsubmit="return confirm('تحديث الحالة إلى مرتجع؟');">
                        @csrf
                        <input type="hidden" name="status" value="returned">
                        <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-undo me-1"></i> مرتجع</button>
                    </form>
                @endif
                @endcan
                <a href="{{ route('admin.checks.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header"><h6 class="mb-0">بيانات الشيك</h6></div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr><td class="text-muted" width="160">رقم الشيك:</td><td>{{ $check->check_number }}</td></tr>
                            <tr><td class="text-muted">المبلغ:</td><td>{{ number_format($check->amount, 2) }}</td></tr>
                            <tr><td class="text-muted">البنك:</td><td>{{ $check->bank_account_id ? ($check->bankAccount->name ?? '—') : ($check->bank_name ?? '—') }}</td></tr>
                            <tr><td class="text-muted">تاريخ الاستحقاق:</td><td>{{ $check->due_date->format('Y-m-d') }}</td></tr>
                            <tr><td class="text-muted">الحالة:</td>
                                <td>
                                    @if($check->status === \App\Models\Check::STATUS_UNDER_COLLECTION)
                                        <span class="badge bg-warning">تحت التحصيل</span>
                                    @elseif($check->status === \App\Models\Check::STATUS_COLLECTED)
                                        <span class="badge bg-success">محصل</span>
                                    @else
                                        <span class="badge bg-danger">مرتجع</span>
                                    @endif
                                </td>
                            </tr>
                            @if($check->salePayment)
                            <tr><td class="text-muted">مرتبط بفاتورة بيع:</td><td><a href="{{ route('admin.sale-invoices.show', $check->salePayment->sale_invoice_id) }}">{{ $check->salePayment->saleInvoice->number ?? '—' }}</a></td></tr>
                            @endif
                            @if($check->supplierPayment)
                            <tr><td class="text-muted">مرتبط بفاتورة شراء:</td><td><a href="{{ route('admin.purchase-invoices.show', $check->supplierPayment->purchase_invoice_id) }}">{{ $check->supplierPayment->purchaseInvoice->number ?? '—' }}</a></td></tr>
                            @endif
                            @if($check->notes)
                            <tr><td class="text-muted">ملاحظات:</td><td>{{ $check->notes }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
