@extends('admin.layouts.master')

@section('page-title')
    نقاط الولاء
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
                    <h5 class="users-page-title">نقاط الولاء</h5>
                    @can('loyalty-adjust')
                        <a href="{{ route('admin.loyalty.adjust-form') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            تعديل نقاط عميل
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="loyalty-filters" action="{{ route('admin.loyalty.index') }}" method="GET" class="users-filters-form">
                        <select name="customer_id" class="users-select">
                            <option value="">جميع العملاء</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>

                        <select name="type" class="users-select">
                            <option value="">جميع الأنواع</option>
                            <option value="earn" {{ request('type') === 'earn' ? 'selected' : '' }}>اكتساب</option>
                            <option value="redeem" {{ request('type') === 'redeem' ? 'selected' : '' }}>استبدال</option>
                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>تعديل</option>
                            <option value="expire" {{ request('type') === 'expire' ? 'selected' : '' }}>انتهاء</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="loyalty-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="loyalty-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>التاريخ</th>
                                    <th style="min-width: 160px;">العميل</th>
                                    <th>النوع</th>
                                    <th>النقاط</th>
                                    <th>الرصيد بعد العملية</th>
                                    <th style="min-width: 200px;">الوصف</th>
                                </tr>
                            </thead>
                            <tbody id="loyalty-body">
                                @include('admin.pages.sales.loyalty.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="loyalty-pagination">
                        @include('admin.pages.sales.loyalty.partials.pagination')
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
            filtersFormId: 'loyalty-filters',
            tableBodyId: 'loyalty-body',
            paginationId: 'loyalty-pagination',
            tableCardId: 'loyalty-card',
            clearBtnId: 'loyalty-clear',
            enableCopy: false,
        });
    </script>
@stop
