@extends('admin.layouts.master')

@section('page-title')
    شجرة الحسابات
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
                    <h5 class="users-page-title">شجرة الحسابات</h5>
                    @can('chart-of-account-create')
                        <a href="{{ route('admin.chart-of-accounts.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة حساب
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="chart-of-accounts-filters" action="{{ route('admin.chart-of-accounts.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالكود أو الاسم..." value="{{ request('query') }}" autocomplete="off">

                        <select name="type" class="users-select">
                            <option value="">جميع الأنواع</option>
                            <option value="asset" {{ request('type') === 'asset' ? 'selected' : '' }}>أصول</option>
                            <option value="liability" {{ request('type') === 'liability' ? 'selected' : '' }}>خصوم</option>
                            <option value="equity" {{ request('type') === 'equity' ? 'selected' : '' }}>حقوق ملكية</option>
                            <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>إيرادات</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>مصروفات</option>
                        </select>

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="chart-of-accounts-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="chart-of-accounts-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الكود</th>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>الحالة</th>
                                    <th>عدد الحركات</th>
                                    <th style="min-width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="chart-of-accounts-body">
                                @include('admin.pages.chart-of-accounts.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="chart-of-accounts-pagination">
                        @include('admin.pages.chart-of-accounts.partials.pagination')
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
            filtersFormId: 'chart-of-accounts-filters',
            tableBodyId: 'chart-of-accounts-body',
            paginationId: 'chart-of-accounts-pagination',
            tableCardId: 'chart-of-accounts-card',
            clearBtnId: 'chart-of-accounts-clear',
            enableCopy: false,
        });
    </script>
@stop
