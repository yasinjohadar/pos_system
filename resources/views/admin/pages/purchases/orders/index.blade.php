@extends('admin.layouts.master')

@section('page-title')
    أوامر الشراء
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
                    <h5 class="users-page-title">أوامر الشراء</h5>
                    @can('purchase-order-create')
                        <a href="{{ route('admin.purchase-orders.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            أمر شراء جديد
                        </a>
                    @endcan
                </div>

                <div class="users-table-card" id="purchase-orders-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الرقم</th>
                                    <th>التاريخ</th>
                                    <th>المورد</th>
                                    <th>الفرع</th>
                                    <th>الإجمالي</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-orders-body">
                                @include('admin.pages.purchases.orders.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="purchase-orders-pagination">
                        @include('admin.pages.purchases.orders.partials.pagination')
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
            tableBodyId: 'purchase-orders-body',
            paginationId: 'purchase-orders-pagination',
            tableCardId: 'purchase-orders-card',
            enableCopy: false,
        });
    </script>
@stop
