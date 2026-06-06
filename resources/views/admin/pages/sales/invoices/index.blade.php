@extends('admin.layouts.master')

@section('page-title')
    فواتير البيع
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">فواتير البيع</h5>
                    @can('sale-invoice-create')
                        <a href="{{ route('admin.sale-invoices.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            فاتورة جديدة
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="sale-invoices-filters" action="{{ route('admin.sale-invoices.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="number" class="users-search-input"
                            placeholder="رقم الفاتورة" value="{{ request('number') }}" autocomplete="off">

                        <select name="branch_id" class="users-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>

                        <select name="customer_id" class="users-select">
                            <option value="">جميع العملاء</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكدة</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        </select>

                        <select name="payment_status" class="users-select">
                            <option value="">حالة الدفع</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>معلق</option>
                            <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>جزئي</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="sale-invoices-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="sale-invoices-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الرقم</th>
                                    <th>التاريخ</th>
                                    <th>الفرع</th>
                                    <th style="min-width: 160px;">العميل</th>
                                    <th>الإجمالي</th>
                                    <th>حالة الدفع</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="sale-invoices-body">
                                @include('admin.pages.sales.invoices.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="sale-invoices-pagination">
                        @include('admin.pages.sales.invoices.partials.pagination')
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('admin.components.delete-confirm-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initIndex({
            filtersFormId: 'sale-invoices-filters',
            tableBodyId: 'sale-invoices-body',
            paginationId: 'sale-invoices-pagination',
            tableCardId: 'sale-invoices-card',
            clearBtnId: 'sale-invoices-clear',
            enableCopy: false,
        });
    </script>
@stop
