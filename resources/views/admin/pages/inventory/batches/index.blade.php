@extends('admin.layouts.master')

@section('page-title')
    دفعات المنتجات
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
                    <h5 class="users-page-title">دفعات المنتجات</h5>
                    @can('product-batch-create')
                        <a href="{{ route('admin.product-batches.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            دفعة جديدة
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="product-batches-filters" action="{{ route('admin.product-batches.index') }}" method="GET" class="users-filters-form">
                        <select name="product_id" class="users-select">
                            <option value="">جميع المنتجات</option>
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        <select name="warehouse_id" class="users-select">
                            <option value="">جميع المخازن</option>
                            @foreach ($warehouses as $w)
                                <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="product-batches-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="product-batches-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>المنتج</th>
                                    <th>رقم الدفعة</th>
                                    <th>المخزن</th>
                                    <th>تاريخ الاستلام</th>
                                    <th>تاريخ الانتهاء</th>
                                    <th>الكمية الحالية</th>
                                    <th style="min-width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="product-batches-body">
                                @include('admin.pages.inventory.batches.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="product-batches-pagination">
                        @include('admin.pages.inventory.batches.partials.pagination')
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
            filtersFormId: 'product-batches-filters',
            tableBodyId: 'product-batches-body',
            paginationId: 'product-batches-pagination',
            tableCardId: 'product-batches-card',
            clearBtnId: 'product-batches-clear',
            enableCopy: false,
        });
    </script>
@stop
