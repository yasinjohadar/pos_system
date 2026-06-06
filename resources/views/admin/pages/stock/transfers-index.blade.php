@extends('admin.layouts.master')

@section('page-title')
    تحويلات المخزون
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
                    <h5 class="users-page-title">تحويلات المخزون</h5>
                    @can('stock-transfer-create')
                        <a href="{{ route('admin.stock.transfers.create') }}" class="users-btn-create">
                            <i class="fas fa-exchange-alt"></i>
                            تحويل جديد
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="stock-transfers-filters" action="{{ route('admin.stock.transfers.index') }}" method="GET" class="users-filters-form">
                        <select name="from_warehouse_id" class="users-select">
                            <option value="">من مخزن</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ request('from_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>

                        <select name="to_warehouse_id" class="users-select">
                            <option value="">إلى مخزن</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ request('to_warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="users-select">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="stock-transfers-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="stock-transfers-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>التاريخ</th>
                                    <th>من مخزن</th>
                                    <th>إلى مخزن</th>
                                    <th>الحالة</th>
                                    <th>المستخدم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="stock-transfers-body">
                                @include('admin.pages.stock.partials.transfers-table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="stock-transfers-pagination">
                        @include('admin.pages.stock.partials.pagination', ['paginator' => $transfers])
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
            filtersFormId: 'stock-transfers-filters',
            tableBodyId: 'stock-transfers-body',
            paginationId: 'stock-transfers-pagination',
            tableCardId: 'stock-transfers-card',
            clearBtnId: 'stock-transfers-clear',
            enableCopy: false,
        });
    </script>
@stop
