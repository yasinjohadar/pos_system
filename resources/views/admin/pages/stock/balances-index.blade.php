@extends('admin.layouts.master')

@section('page-title')
    أرصدة المخزون
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">أرصدة المخزون</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.stock.movements.index') }}" class="users-btn-secondary">
                            <i class="fas fa-exchange-alt"></i>
                            حركات المخزون
                        </a>
                        <a href="{{ route('admin.stock.balances.index', ['low_stock' => 1]) }}" class="users-btn-edit" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);">
                            <i class="fas fa-bell"></i>
                            تنبيهات الانخفاض
                        </a>
                    </div>
                </div>

                <div class="users-filters-card">
                    <form id="stock-balances-filters" action="{{ route('admin.stock.balances.index') }}" method="GET" class="users-filters-form">
                        <select name="warehouse_id" class="users-select">
                            <option value="">جميع المخازن</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>

                        <label class="users-filter-check">
                            <input type="checkbox" name="low_stock" value="1" class="users-filter-checkbox" {{ request('low_stock') ? 'checked' : '' }}>
                            <span>تنبيه انخفاض فقط</span>
                        </label>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="stock-balances-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="stock-balances-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">المنتج</th>
                                    <th>المخزن</th>
                                    <th>الرصيد</th>
                                    <th>حد التنبيه</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody id="stock-balances-body">
                                @include('admin.pages.stock.partials.balances-table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="stock-balances-pagination">
                        @include('admin.pages.stock.partials.pagination', ['paginator' => $balances])
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
            filtersFormId: 'stock-balances-filters',
            tableBodyId: 'stock-balances-body',
            paginationId: 'stock-balances-pagination',
            tableCardId: 'stock-balances-card',
            clearBtnId: 'stock-balances-clear',
            enableCopy: false,
        });
    </script>
@stop
