@extends('admin.layouts.master')

@section('page-title')
    تعديل فاتورة البيع {{ $saleInvoice->number }}
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
                    <h5 class="users-page-title">تعديل فاتورة: {{ $saleInvoice->number }}</h5>
                    <a href="{{ route('admin.sale-invoices.show', $saleInvoice) }}" class="users-btn-secondary">
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

                @include('admin.pages.sales.invoices.partials.form', [
                    'action' => route('admin.sale-invoices.update', $saleInvoice),
                    'method' => 'PUT',
                    'saleInvoice' => $saleInvoice,
                    'cancelUrl' => route('admin.sale-invoices.show', $saleInvoice),
                    'oldProducts' => $oldProducts,
                    'selectedCustomer' => $selectedCustomer ?? null,
                    'submitLabel' => 'حفظ التعديلات',
                ])

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.product-select-scripts')
    @include('admin.components.premium.scripts')
    @include('admin.pages.sales.invoices.partials.form-script')
@stop
