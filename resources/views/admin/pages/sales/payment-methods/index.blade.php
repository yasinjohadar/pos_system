@extends('admin.layouts.master')

@section('page-title')
    طرق الدفع
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
                    <h5 class="users-page-title">طرق الدفع</h5>
                    @can('payment-method-create')
                        <a href="{{ route('admin.payment-methods.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة طريقة دفع
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="payment-methods-filters" action="{{ route('admin.payment-methods.index') }}" method="GET" class="users-filters-form">
                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="payment-methods-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="payment-methods-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 160px;">الاسم</th>
                                    <th>الكود</th>
                                    <th>الترتيب</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="payment-methods-body">
                                @include('admin.pages.sales.payment-methods.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="payment-methods-pagination">
                        @include('admin.pages.sales.payment-methods.partials.pagination')
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
            filtersFormId: 'payment-methods-filters',
            tableBodyId: 'payment-methods-body',
            paginationId: 'payment-methods-pagination',
            tableCardId: 'payment-methods-card',
            clearBtnId: 'payment-methods-clear',
            enableCopy: false,
        });
    </script>
@stop
