@extends('admin.layouts.master')

@section('page-title')
    الحسابات البنكية
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
                    <h5 class="users-page-title">الحسابات البنكية</h5>
                    @can('bank-account-create')
                        <a href="{{ route('admin.bank-accounts.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            إضافة حساب بنكي
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="bank-accounts-filters" action="{{ route('admin.bank-accounts.index') }}" method="GET" class="users-filters-form">
                        <select name="is_active" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="bank-accounts-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="bank-accounts-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 160px;">البنك</th>
                                    <th>رقم الحساب</th>
                                    <th>الفرع</th>
                                    <th>رصيد افتتاحي</th>
                                    <th>العملة</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="bank-accounts-body">
                                @include('admin.pages.sales.bank-accounts.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="bank-accounts-pagination">
                        @include('admin.pages.sales.bank-accounts.partials.pagination')
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
            filtersFormId: 'bank-accounts-filters',
            tableBodyId: 'bank-accounts-body',
            paginationId: 'bank-accounts-pagination',
            tableCardId: 'bank-accounts-card',
            clearBtnId: 'bank-accounts-clear',
            enableCopy: false,
        });
    </script>
@stop
