@extends('admin.layouts.master')

@section('page-title')
    فواتير الشراء
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
                    <h5 class="users-page-title">فواتير الشراء</h5>
                    @can('purchase-invoice-create')
                        <a href="{{ route('admin.purchase-invoices.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            فاتورة شراء جديدة
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="purchase-invoices-filters" action="{{ route('admin.purchase-invoices.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="number" class="users-search-input"
                            placeholder="رقم الفاتورة" value="{{ request('number') }}" autocomplete="off">

                        <select name="branch_id" class="users-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>

                        <select name="supplier_id" class="users-select">
                            <option value="">جميع الموردين</option>
                            @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>معتمدة</option>
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
                        <button type="button" id="purchase-invoices-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="purchase-invoices-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الرقم</th>
                                    <th>التاريخ</th>
                                    <th>الفرع</th>
                                    <th style="min-width: 160px;">المورد</th>
                                    <th>الإجمالي</th>
                                    <th>حالة الدفع</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-invoices-body">
                                @include('admin.pages.purchases.invoices.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="purchase-invoices-pagination">
                        @include('admin.pages.purchases.invoices.partials.pagination')
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
            filtersFormId: 'purchase-invoices-filters',
            tableBodyId: 'purchase-invoices-body',
            paginationId: 'purchase-invoices-pagination',
            tableCardId: 'purchase-invoices-card',
            clearBtnId: 'purchase-invoices-clear',
            enableCopy: false,
        });
    </script>
@stop
