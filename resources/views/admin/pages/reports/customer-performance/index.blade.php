@extends('admin.layouts.master')

@section('page-title')
    تقرير أداء العملاء
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>@include('admin.pages.reports.product-performance.partials.styles')</style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">تقرير أداء العملاء</h5>
                    <a href="{{ route('admin.reports.customer-performance.index', array_merge(request()->only(['from', 'to']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="customer-performance-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                @include('admin.pages.reports.customer-performance.partials.nav', ['active' => 'index'])

                <div class="users-filters-card">
                    <form id="customer-performance-filters" action="{{ route('admin.reports.customer-performance.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from" class="users-search-input users-filter-date"
                            value="{{ $from }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date"
                            value="{{ $to }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="customer-performance-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="customer-performance-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">العميل</th>
                                    <th>عدد الفواتير</th>
                                    <th>إجمالي المبيعات</th>
                                    <th>متوسط الفاتورة</th>
                                    <th>آخر شراء</th>
                                </tr>
                            </thead>
                            <tbody id="customer-performance-body">
                                @include('admin.pages.reports.customer-performance.partials.table-rows')
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        (function () {
            var filtersForm = document.getElementById('customer-performance-filters');
            var tableBody = document.getElementById('customer-performance-body');
            var tableCard = document.getElementById('customer-performance-card');
            var clearBtn = document.getElementById('customer-performance-clear');
            var exportLink = document.getElementById('customer-performance-export');
            var isLoading = false;

            function getParams() {
                return new URLSearchParams(new FormData(filtersForm));
            }

            function updateExportLink() {
                if (!exportLink) return;
                var params = getParams();
                params.set('format', 'csv');
                exportLink.href = filtersForm.action + '?' + params.toString();
            }

            function updateUrl(params) {
                var url = new URL(window.location.href);
                url.search = params.toString();
                window.history.replaceState({}, '', url);
                updateExportLink();
            }

            function fetchReport() {
                if (!filtersForm || !tableBody || isLoading) return;

                var params = getParams();
                isLoading = true;
                if (tableCard) tableCard.classList.add('users-table-card--loading');
                updateUrl(params);

                fetch(filtersForm.action + '?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                })
                    .then(function (r) {
                        if (!r.ok) throw new Error('Network error');
                        return r.json();
                    })
                    .then(function (data) {
                        tableBody.innerHTML = data.tbody;
                    })
                    .catch(function () {
                        AdminPremium.showToast('حدث خطأ أثناء تحميل التقرير', 'error');
                    })
                    .finally(function () {
                        isLoading = false;
                        if (tableCard) tableCard.classList.remove('users-table-card--loading');
                    });
            }

            if (filtersForm) {
                filtersForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetchReport();
                });

                filtersForm.querySelectorAll('.users-filter-date').forEach(function (input) {
                    input.addEventListener('change', fetchReport);
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    var today = new Date();
                    var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    filtersForm.from.value = firstDay.toISOString().slice(0, 10);
                    filtersForm.to.value = today.toISOString().slice(0, 10);
                    fetchReport();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
