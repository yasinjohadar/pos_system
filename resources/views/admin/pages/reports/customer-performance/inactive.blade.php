@extends('admin.layouts.master')

@section('page-title')
    عملاء غير نشطين
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
                    <h5 class="users-page-title">عملاء غير نشطين</h5>
                    <a href="{{ route('admin.reports.customer-performance.inactive', array_merge(request()->only(['days']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="customer-inactive-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                @include('admin.pages.reports.customer-performance.partials.nav', ['active' => 'inactive'])

                <div class="users-filters-card">
                    <form id="customer-inactive-filters" action="{{ route('admin.reports.customer-performance.inactive') }}" method="GET" class="users-filters-form">
                        <input type="number" name="days" class="users-search-input" style="max-width: 160px;"
                            value="{{ $days }}" min="1" max="365" title="لم يشتروا منذ (يوم)">
                        <span class="users-muted-text" style="align-self: center; font-size: 0.875rem;">يوم بدون شراء</span>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="customer-inactive-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="customer-inactive-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">العميل</th>
                                    <th>الهاتف</th>
                                    <th>البريد</th>
                                </tr>
                            </thead>
                            <tbody id="customer-inactive-body">
                                @include('admin.pages.reports.customer-performance.partials.inactive-rows')
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
            var filtersForm = document.getElementById('customer-inactive-filters');
            var tableBody = document.getElementById('customer-inactive-body');
            var tableCard = document.getElementById('customer-inactive-card');
            var clearBtn = document.getElementById('customer-inactive-clear');
            var exportLink = document.getElementById('customer-inactive-export');
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

                filtersForm.querySelectorAll('input[name="days"]').forEach(function (input) {
                    input.addEventListener('change', fetchReport);
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    filtersForm.days.value = '90';
                    fetchReport();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
