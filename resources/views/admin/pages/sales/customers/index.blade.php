@extends('admin.layouts.master')

@section('page-title')
    العملاء
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
                    <h5 class="users-page-title">العملاء</h5>
                    @can('customer-create')
                        <a href="{{ route('admin.customers.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة عميل
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="customers-filters" action="{{ route('admin.customers.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث (الاسم، الهاتف، البريد)" value="{{ request('query') }}" autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="customers-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="customers-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">الاسم</th>
                                    <th style="min-width: 140px;">الهاتف</th>
                                    <th style="min-width: 200px;">البريد</th>
                                    <th>التصنيف</th>
                                    <th>نقاط الولاء</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="customers-body">
                                @include('admin.pages.sales.customers.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="customers-pagination">
                        @include('admin.pages.sales.customers.partials.pagination')
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
            filtersFormId: 'customers-filters',
            tableBodyId: 'customers-body',
            paginationId: 'customers-pagination',
            tableCardId: 'customers-card',
            clearBtnId: 'customers-clear',
        });
    </script>
@stop
