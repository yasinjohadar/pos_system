@extends('admin.layouts.master')

@section('page-title')
    العروض والخصومات
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
                    <h5 class="users-page-title">العروض والخصومات</h5>
                    @can('promotion-create')
                        <a href="{{ route('admin.promotions.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة عرض جديد
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="promotions-filters" action="{{ route('admin.promotions.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالاسم" value="{{ request('query') }}" autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>متوقف</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="promotions-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="promotions-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 160px;">الاسم</th>
                                    <th>النوع</th>
                                    <th>القيمة</th>
                                    <th>الفترة</th>
                                    <th>الحد الأدنى للكمية</th>
                                    <th>الحالة</th>
                                    <th>عدد المنتجات</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="promotions-body">
                                @include('admin.pages.sales.promotions.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="promotions-pagination">
                        @include('admin.pages.sales.promotions.partials.pagination')
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
            filtersFormId: 'promotions-filters',
            tableBodyId: 'promotions-body',
            paginationId: 'promotions-pagination',
            tableCardId: 'promotions-card',
            clearBtnId: 'promotions-clear',
            enableCopy: false,
        });
    </script>
@stop
