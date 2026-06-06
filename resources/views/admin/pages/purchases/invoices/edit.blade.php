@extends('admin.layouts.master')

@section('page-title')
    تعديل فاتورة الشراء {{ $purchaseInvoice->number }}
@stop

@section('css')
    @include('admin.components.premium.styles')
    @include('admin.components.premium.product-select-assets')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل فاتورة: {{ $purchaseInvoice->number }}</h5>
                    <a href="{{ route('admin.purchase-invoices.show', $purchaseInvoice) }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @include('admin.pages.purchases.invoices.partials.form', [
                    'action' => route('admin.purchase-invoices.update', $purchaseInvoice),
                    'method' => 'PUT',
                    'purchaseInvoice' => $purchaseInvoice,
                    'cancelUrl' => route('admin.purchase-invoices.show', $purchaseInvoice),
                    'oldProducts' => $oldProducts,
                    'selectedSupplier' => $selectedSupplier ?? null,
                    'submitLabel' => 'تحديث الفاتورة',
                ])

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.product-select-scripts')
    @include('admin.components.premium.scripts')
    @include('admin.pages.purchases.invoices.partials.form-script')
@stop
