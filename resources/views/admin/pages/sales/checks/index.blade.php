@extends('admin.layouts.master')

@section('page-title')
    الشيكات
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
                    <h5 class="users-page-title">الشيكات</h5>
                    @can('check-create')
                        <a href="{{ route('admin.checks.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة شيك
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="checks-filters" action="{{ route('admin.checks.index') }}" method="GET" class="users-filters-form">
                        <select name="status" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="{{ \App\Models\Check::STATUS_UNDER_COLLECTION }}" {{ request('status') === \App\Models\Check::STATUS_UNDER_COLLECTION ? 'selected' : '' }}>تحت التحصيل</option>
                            <option value="{{ \App\Models\Check::STATUS_COLLECTED }}" {{ request('status') === \App\Models\Check::STATUS_COLLECTED ? 'selected' : '' }}>محصل</option>
                            <option value="{{ \App\Models\Check::STATUS_RETURNED }}" {{ request('status') === \App\Models\Check::STATUS_RETURNED ? 'selected' : '' }}>مرتجع</option>
                        </select>

                        <input type="date" name="from_date" class="users-search-input users-filter-date"
                            value="{{ request('from_date') }}" title="من تاريخ">
                        <input type="date" name="to_date" class="users-search-input users-filter-date"
                            value="{{ request('to_date') }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="checks-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="checks-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>رقم الشيك</th>
                                    <th>البنك</th>
                                    <th>المبلغ</th>
                                    <th>تاريخ الاستحقاق</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="checks-body">
                                @include('admin.pages.sales.checks.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="checks-pagination">
                        @include('admin.pages.sales.checks.partials.pagination')
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
            filtersFormId: 'checks-filters',
            tableBodyId: 'checks-body',
            paginationId: 'checks-pagination',
            tableCardId: 'checks-card',
            clearBtnId: 'checks-clear',
            enableCopy: false,
        });
    </script>
@stop
