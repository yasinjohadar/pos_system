@extends('admin.layouts.master')

@section('page-title')
    الوحدات
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
                    <h5 class="users-page-title">الوحدات</h5>
                    @can('unit-create')
                        <a href="{{ route('admin.units.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة وحدة
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="units-filters-form" action="{{ route('admin.units.index') }}" method="GET" class="users-filters-form">
                        <input type="text" name="query" class="users-search-input"
                            placeholder="بحث بالاسم أو الرمز" value="{{ request('query') }}"
                            autocomplete="off">

                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="units-filters-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="units-table-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 180px;">الاسم</th>
                                    <th style="min-width: 90px;">الرمز</th>
                                    <th style="min-width: 150px;">الوحدة الأساسية</th>
                                    <th style="min-width: 120px;">معامل التحويل</th>
                                    <th style="min-width: 110px;">عدد المنتجات</th>
                                    <th style="min-width: 130px;">الحالة النشطة</th>
                                    <th style="min-width: 140px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="units-table-body">
                                @include('admin.pages.units.partials.table-rows')
                            </tbody>
                        </table>
                    </div>

                    <div class="users-pagination" id="units-pagination">
                        @include('admin.pages.units.partials.pagination')
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
            filtersFormId: 'units-filters-form',
            tableBodyId: 'units-table-body',
            paginationId: 'units-pagination',
            tableCardId: 'units-table-card',
            clearBtnId: 'units-filters-clear',
            enableCopy: false,
            toggleMessages: {
                confirmActive: 'هل أنت متأكد من تفعيل هذه الوحدة؟',
                confirmInactive: 'هل أنت متأكد من إيقاف تفعيل هذه الوحدة؟',
                error: 'حدث خطأ أثناء تحديث حالة الوحدة',
            },
        });
    </script>
@stop
