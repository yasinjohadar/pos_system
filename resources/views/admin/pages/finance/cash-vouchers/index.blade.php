@extends('admin.layouts.master')

@section('page-title')
    سندات القبض والصرف
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
                    <h5 class="users-page-title">سندات القبض والصرف</h5>
                    @can('cash-voucher-create')
                        <a href="{{ route('admin.cash-vouchers.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            سند جديد
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="cash-vouchers-filters" action="{{ route('admin.cash-vouchers.index') }}" method="GET" class="users-filters-form">
                        <select name="type" class="users-select">
                            <option value="">النوع — الكل</option>
                            <option value="receipt" {{ request('type') === 'receipt' ? 'selected' : '' }}>قبض</option>
                            <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>صرف</option>
                        </select>

                        <select name="treasury_id" class="users-select">
                            <option value="">الخزنة — الكل</option>
                            @foreach ($treasuries as $t)
                                <option value="{{ $t->id }}" {{ request('treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>

                        <select name="bank_account_id" class="users-select">
                            <option value="">الحساب البنكي — الكل</option>
                            @foreach ($bankAccounts as $ba)
                                <option value="{{ $ba->id }}" {{ request('bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="cash-vouchers-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="cash-vouchers-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>الرقم</th>
                                    <th>التاريخ</th>
                                    <th>النوع</th>
                                    <th>الخزنة / البنك</th>
                                    <th>الفئة</th>
                                    <th>المبلغ</th>
                                    <th style="min-width: 130px;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="cash-vouchers-body">
                                @include('admin.pages.finance.cash-vouchers.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="cash-vouchers-pagination">
                        @include('admin.pages.finance.cash-vouchers.partials.pagination')
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
            filtersFormId: 'cash-vouchers-filters',
            tableBodyId: 'cash-vouchers-body',
            paginationId: 'cash-vouchers-pagination',
            tableCardId: 'cash-vouchers-card',
            clearBtnId: 'cash-vouchers-clear',
            enableCopy: false,
        });
    </script>
@stop
