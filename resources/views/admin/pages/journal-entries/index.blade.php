@extends('admin.layouts.master')

@section('page-title')
    القيود اليومية
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
                    <h5 class="users-page-title">القيود اليومية</h5>
                    @can('journal-entry-create')
                        <a href="{{ route('admin.journal-entries.create') }}" class="users-btn-create">
                            <i class="fas fa-plus"></i>
                            قيد يدوي
                        </a>
                    @endcan
                </div>

                <div class="users-filters-card">
                    <form id="journal-entries-filters" action="{{ route('admin.journal-entries.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from" class="users-search-input users-filter-date"
                            value="{{ request('from') }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date"
                            value="{{ request('to') }}" title="إلى تاريخ">

                        <select name="reference_type" class="users-select">
                            <option value="">المرجع — الكل</option>
                            <option value="App\Models\SaleInvoice" {{ request('reference_type') === 'App\Models\SaleInvoice' ? 'selected' : '' }}>فاتورة بيع</option>
                            <option value="App\Models\PurchaseInvoice" {{ request('reference_type') === 'App\Models\PurchaseInvoice' ? 'selected' : '' }}>فاتورة شراء</option>
                            <option value="App\Models\CashVoucher" {{ request('reference_type') === 'App\Models\CashVoucher' ? 'selected' : '' }}>سند قبض/صرف</option>
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <button type="button" id="journal-entries-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="journal-entries-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>رقم القيد</th>
                                    <th>التاريخ</th>
                                    <th>الوصف</th>
                                    <th>المرجع</th>
                                    <th>أنشئ بواسطة</th>
                                    <th style="min-width: 80px;">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody id="journal-entries-body">
                                @include('admin.pages.journal-entries.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                    <div class="users-pagination" id="journal-entries-pagination">
                        @include('admin.pages.journal-entries.partials.pagination')
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
            filtersFormId: 'journal-entries-filters',
            tableBodyId: 'journal-entries-body',
            paginationId: 'journal-entries-pagination',
            tableCardId: 'journal-entries-card',
            clearBtnId: 'journal-entries-clear',
            enableCopy: false,
        });
    </script>
@stop
