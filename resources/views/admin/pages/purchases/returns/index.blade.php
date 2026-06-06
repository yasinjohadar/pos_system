@extends('admin.layouts.master')

@section('page-title')
    مرتجعات الشراء
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
                    <h5 class="users-page-title">مرتجعات الشراء</h5>
                    @can('purchase-return-create')
                        <a href="{{ route('admin.purchase-returns.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            مرتجع جديد
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="purchase-returns-filters" action="{{ route('admin.purchase-returns.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="return_number" class="users-search-input"
                            placeholder="رقم المرتجع" value="{{ request('return_number') }}" autocomplete="off">

                        <input type="number" name="purchase_invoice_id" class="users-search-input"
                            placeholder="رقم الفاتورة (ID)" value="{{ request('purchase_invoice_id') }}" autocomplete="off">

                        <select name="status" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغى</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="purchase-returns-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="purchase-returns-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>رقم المرتجع</th>
                                    <th>الفاتورة الأصلية</th>
                                    <th>التاريخ</th>
                                    <th>المخزن</th>
                                    <th>المبلغ المرتجع</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-returns-body">
                                @include('admin.pages.purchases.returns.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="purchase-returns-pagination">
                        @include('admin.pages.purchases.returns.partials.pagination')
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initIndex({
            filtersFormId: 'purchase-returns-filters',
            tableBodyId: 'purchase-returns-body',
            paginationId: 'purchase-returns-pagination',
            tableCardId: 'purchase-returns-card',
            clearBtnId: 'purchase-returns-clear',
            enableCopy: false,
        });
    </script>
@stop
