@extends('admin.layouts.master')

@section('page-title')
    السنوات المالية
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
                    <h5 class="users-page-title">السنوات المالية</h5>
                    <a href="{{ route('admin.fiscal-years.create') }}" class="users-btn-create">
                        <i class="fas fa-plus"></i>
                        سنة مالية جديدة
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="fiscal-years-filters" action="{{ route('admin.fiscal-years.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالاسم..." value="{{ request('query') }}" autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">النشطة — الكل</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشطة</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشطة</option>
                        </select>

                        <select name="is_closed" class="users-select">
                            <option value="">المقفلة — الكل</option>
                            <option value="1" {{ request('is_closed') === '1' ? 'selected' : '' }}>مقفلة</option>
                            <option value="0" {{ request('is_closed') === '0' ? 'selected' : '' }}>مفتوحة</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="fiscal-years-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="fiscal-years-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الاسم</th>
                                    <th>من</th>
                                    <th>إلى</th>
                                    <th>نشطة</th>
                                    <th>مقفلة</th>
                                    <th style="min-width: 80px;">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="fiscal-years-body">
                                @include('admin.pages.finance.fiscal-years.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="fiscal-years-pagination">
                        @include('admin.pages.finance.fiscal-years.partials.pagination')
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
            filtersFormId: 'fiscal-years-filters',
            tableBodyId: 'fiscal-years-body',
            paginationId: 'fiscal-years-pagination',
            tableCardId: 'fiscal-years-card',
            clearBtnId: 'fiscal-years-clear',
            enableCopy: false,
        });
    </script>
@stop
