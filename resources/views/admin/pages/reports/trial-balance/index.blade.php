@extends('admin.layouts.master')

@section('page-title')
    ميزان المراجعة
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
                    <h5 class="users-page-title">ميزان المراجعة</h5>
                    <a href="{{ route('admin.reports.trial-balance.index', array_merge(request()->only(['from', 'to']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="trial-balance-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="trial-balance-filters" action="{{ route('admin.reports.trial-balance.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from" class="users-search-input users-filter-date"
                            value="{{ $from }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date"
                            value="{{ $to }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="trial-balance-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="trial-balance-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>كود الحساب</th>
                                    <th>اسم الحساب</th>
                                    <th>نوع الحساب</th>
                                    <th>إجمالي المدين</th>
                                    <th>إجمالي الدائن</th>
                                    <th>رصيد مدين</th>
                                    <th>رصيد دائن</th>
                                </tr>
                            </thead>
                            <tbody id="trial-balance-body">
                                @include('admin.pages.reports.trial-balance.partials.table-rows')
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
            var filtersForm = document.getElementById('trial-balance-filters');
            var tableBody = document.getElementById('trial-balance-body');
            var tableCard = document.getElementById('trial-balance-card');
            var clearBtn = document.getElementById('trial-balance-clear');
            var exportLink = document.getElementById('trial-balance-export');
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
