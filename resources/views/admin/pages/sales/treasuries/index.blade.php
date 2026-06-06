@extends('admin.layouts.master')

@section('page-title')
    الخزائن والبنوك
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
                    <h5 class="users-page-title">الخزائن والبنوك</h5>
                    @can('treasury-create')
                        <a href="{{ route('admin.treasuries.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة خزنة / بنك
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="treasuries-filters" action="{{ route('admin.treasuries.index') }}" method="GET" class="users-filters-form">
                        <select name="type" class="users-select">
                            <option value="">جميع الأنواع</option>
                            <option value="cashbox" {{ request('type') === 'cashbox' ? 'selected' : '' }}>خزنة</option>
                            <option value="bank" {{ request('type') === 'bank' ? 'selected' : '' }}>بنك</option>
                        </select>

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="treasuries-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="treasuries-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 160px;">الاسم</th>
                                    <th>النوع</th>
                                    <th>الفرع</th>
                                    <th>رصيد افتتاحي</th>
                                    <th>العملة</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="treasuries-body">
                                @include('admin.pages.sales.treasuries.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="treasuries-pagination">
                        @include('admin.pages.sales.treasuries.partials.pagination')
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
            filtersFormId: 'treasuries-filters',
            tableBodyId: 'treasuries-body',
            paginationId: 'treasuries-pagination',
            tableCardId: 'treasuries-card',
            clearBtnId: 'treasuries-clear',
            enableCopy: false,
        });
    </script>
@stop
