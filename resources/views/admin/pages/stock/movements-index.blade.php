@extends('admin.layouts.master')

@section('page-title')
    حركات المخزون
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
                    <h5 class="users-page-title">حركات المخزون</h5>
                    <div class="users-header-actions">
                        @can('stock-movement-create')
                            <a href="{{ route('admin.stock.movements.create') }}" class="users-btn-create">
                                <i class="fas fa-plus"></i>
                                حركة جديدة
                            </a>
                        @endcan
                        <a href="{{ route('admin.stock.balances.index') }}" class="users-btn-secondary">
                            <i class="fas fa-warehouse"></i>
                            أرصدة المخزون
                        </a>
                    </div>
                </div>

                <div class="users-filters-card">
                    <form id="stock-movements-filters" action="{{ route('admin.stock.movements.index') }}" method="GET" class="users-filters-form">
                        <select name="warehouse_id" class="users-select">
                            <option value="">جميع المخازن</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>

                        <select name="type" class="users-select">
                            <option value="">جميع الأنواع</option>
                            @foreach (\App\Models\StockMovement::TYPE_LABELS as $k => $v)
                                <option value="{{ $k }}" {{ request('type') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>

                        <input type="date" name="from_date" class="users-search-input users-filter-date" value="{{ request('from_date') }}" title="من تاريخ">
                        <input type="date" name="to_date" class="users-search-input users-filter-date" value="{{ request('to_date') }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="stock-movements-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="stock-movements-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>التاريخ</th>
                                    <th style="min-width: 180px;">المنتج</th>
                                    <th>المخزن</th>
                                    <th>النوع</th>
                                    <th>الكمية</th>
                                    <th>المستخدم</th>
                                    <th>ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody id="stock-movements-body">
                                @include('admin.pages.stock.partials.movements-table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="stock-movements-pagination">
                        @include('admin.pages.stock.partials.pagination', ['paginator' => $movements])
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
            filtersFormId: 'stock-movements-filters',
            tableBodyId: 'stock-movements-body',
            paginationId: 'stock-movements-pagination',
            tableCardId: 'stock-movements-card',
            clearBtnId: 'stock-movements-clear',
            enableCopy: false,
        });
    </script>
@stop
