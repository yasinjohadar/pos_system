@extends('admin.layouts.master')

@section('page-title')
    كوبونات الخصم
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
                    <h5 class="users-page-title">كوبونات الخصم</h5>
                    @can('coupon-create')
                        <a href="{{ route('admin.coupons.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة كوبون
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="coupons-filters" action="{{ route('admin.coupons.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="code" class="users-search-input"
                            placeholder="كود الكوبون" value="{{ request('code') }}" autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="coupons-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="coupons-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الكود</th>
                                    <th>النوع</th>
                                    <th>القيمة</th>
                                    <th>الحد الأدنى للطلب</th>
                                    <th>المستخدم / الأقصى</th>
                                    <th>الصلاحية</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="coupons-body">
                                @include('admin.pages.sales.coupons.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="coupons-pagination">
                        @include('admin.pages.sales.coupons.partials.pagination')
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
            filtersFormId: 'coupons-filters',
            tableBodyId: 'coupons-body',
            paginationId: 'coupons-pagination',
            tableCardId: 'coupons-card',
            clearBtnId: 'coupons-clear',
            enableCopy: false,
        });
    </script>
@stop
