@extends('admin.layouts.master')

@section('page-title')
    الضرائب
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
                    <h5 class="users-page-title">الضرائب</h5>
                    @can('tax-create')
                        <a href="{{ route('admin.taxes.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة ضريبة
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="taxes-filters" action="{{ route('admin.taxes.index') }}" method="GET" class="users-filters-form">
                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="taxes-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="taxes-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الاسم</th>
                                    <th>النوع</th>
                                    <th>النسبة / القيمة</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="taxes-body">
                                @include('admin.pages.taxes.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="taxes-pagination">
                        @include('admin.pages.taxes.partials.pagination')
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
            filtersFormId: 'taxes-filters',
            tableBodyId: 'taxes-body',
            paginationId: 'taxes-pagination',
            tableCardId: 'taxes-card',
            clearBtnId: 'taxes-clear',
            enableCopy: false,
        });
    </script>
@stop
