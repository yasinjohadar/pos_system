@extends('admin.layouts.master')

@section('page-title')
    التحويلات المالية
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
                    <h5 class="users-page-title">التحويلات المالية</h5>
                    @can('financial-transfer-create')
                        <a href="{{ route('admin.financial-transfers.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            تحويل جديد
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="financial-transfers-filters" action="{{ route('admin.financial-transfers.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from_date" class="users-search-input users-filter-date"
                            value="{{ request('from_date') }}" title="من تاريخ">
                        <input type="date" name="to_date" class="users-search-input users-filter-date"
                            value="{{ request('to_date') }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="financial-transfers-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="financial-transfers-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>التاريخ</th>
                                    <th style="min-width: 160px;">من</th>
                                    <th style="min-width: 160px;">إلى</th>
                                    <th>المبلغ</th>
                                    <th>المرجع</th>
                                    <th style="min-width: 140px;">المستخدم</th>
                                </tr>
                            </thead>
                            <tbody id="financial-transfers-body">
                                @include('admin.pages.sales.financial-transfers.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="financial-transfers-pagination">
                        @include('admin.pages.sales.financial-transfers.partials.pagination')
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
            filtersFormId: 'financial-transfers-filters',
            tableBodyId: 'financial-transfers-body',
            paginationId: 'financial-transfers-pagination',
            tableCardId: 'financial-transfers-card',
            clearBtnId: 'financial-transfers-clear',
            enableCopy: false,
        });
    </script>
@stop
