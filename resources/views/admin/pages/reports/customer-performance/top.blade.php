@extends('admin.layouts.master')

@section('page-title')
    أفضل العملاء
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
                    <h5 class="users-page-title">أفضل العملاء</h5>
                    <a href="{{ route('admin.reports.customer-performance.top', array_merge(request()->only(['from', 'to', 'limit']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="customer-top-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                @include('admin.pages.reports.customer-performance.partials.nav', ['active' => 'top'])

                <div class="users-filters-card">
                    <form id="customer-top-filters" action="{{ route('admin.reports.customer-performance.top') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from" class="users-search-input users-filter-date"
                            value="{{ $from }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date"
                            value="{{ $to }}" title="إلى تاريخ">
                        <input type="number" name="limit" class="users-search-input" style="max-width: 120px;"
                            value="{{ $limit }}" min="5" max="50" title="عدد العملاء">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="customer-top-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="customer-top-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">العميل</th>
                                    <th>عدد الفواتير</th>
                                    <th>إجمالي المبيعات</th>
                                    <th>متوسط الفاتورة</th>
                                </tr>
                            </thead>
                            <tbody id="customer-top-body">
                                @include('admin.pages.reports.customer-performance.partials.top-rows')
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
            var filtersForm = document.getElementById('customer-top-filters');
            var tableBody = document.getElementById('customer-top-body');
            var tableCard = document.getElementById('customer-top-card');
            var clearBtn = document.getElementById('customer-top-clear');
            var exportLink = document.getElementById('customer-top-export');
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

                filtersForm.querySelectorAll('.users-filter-date, input[name="limit"]').forEach(function (input) {
                    input.addEventListener('change', fetchReport);
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    var today = new Date();
                    var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    filtersForm.from.value = firstDay.toISOString().slice(0, 10);
                    filtersForm.to.value = today.toISOString().slice(0, 10);
                    filtersForm.limit.value = '10';
                    fetchReport();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
