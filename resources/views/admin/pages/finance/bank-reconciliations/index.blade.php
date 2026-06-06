@extends('admin.layouts.master')

@section('page-title')
    التسويات البنكية
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
                    <h5 class="users-page-title">التسويات البنكية</h5>
                    @can('bank-reconciliation-create')
                        <a href="{{ route('admin.bank-reconciliations.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            تسوية جديدة
                        </a>
                    @endcan
                </div>

                <div class="users-table-card" id="bank-reconciliations-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الحساب البنكي</th>
                                    <th>تاريخ الكشف</th>
                                    <th>رصيد الكشف</th>
                                    <th>رصيد الدفاتر</th>
                                    <th>الفرق</th>
                                    <th>الحالة</th>
                                    <th style="min-width: 100px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="bank-reconciliations-body">
                                @include('admin.pages.finance.bank-reconciliations.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="bank-reconciliations-pagination">
                        @include('admin.pages.finance.bank-reconciliations.partials.pagination')
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
            filtersFormId: null,
            tableBodyId: 'bank-reconciliations-body',
            paginationId: 'bank-reconciliations-pagination',
            tableCardId: 'bank-reconciliations-card',
            enableCopy: false,
        });
    </script>
@stop
