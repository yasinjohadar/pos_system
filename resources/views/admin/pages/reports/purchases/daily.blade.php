@extends('admin.layouts.master')

@section('page-title')
    تقرير المشتريات اليومي
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>
        .users-report-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }
        @media (max-width: 991px) {
            .users-report-kpi-grid { grid-template-columns: 1fr; }
        }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">تقرير المشتريات اليومي</h5>
                    <a href="{{ route('admin.reports.purchases.daily', array_merge(request()->only(['date', 'branch_id']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="purchases-daily-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="purchases-daily-filters" action="{{ route('admin.reports.purchases.daily') }}" method="GET" class="users-filters-form">
                        <input type="date" name="date" class="users-search-input users-filter-date"
                            value="{{ $date->format('Y-m-d') }}" title="التاريخ">

                        <select name="branch_id" class="users-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ (string) $branchId === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="purchases-daily-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div id="purchases-daily-card" class="users-table-card--loading-target" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                    <div id="purchases-daily-summary">
                        @include('admin.pages.reports.purchases.partials.daily-summary')
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
            var filtersForm = document.getElementById('purchases-daily-filters');
            var summaryEl = document.getElementById('purchases-daily-summary');
            var reportCard = document.getElementById('purchases-daily-card');
            var clearBtn = document.getElementById('purchases-daily-clear');
            var exportLink = document.getElementById('purchases-daily-export');
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
                if (!filtersForm || !summaryEl || isLoading) return;

                var params = getParams();
                isLoading = true;
                if (reportCard) reportCard.classList.add('users-table-card--loading');
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
                        summaryEl.innerHTML = data.summary;
                    })
                    .catch(function () {
                        AdminPremium.showToast('حدث خطأ أثناء تحميل التقرير', 'error');
                    })
                    .finally(function () {
                        isLoading = false;
                        if (reportCard) reportCard.classList.remove('users-table-card--loading');
                    });
            }

            if (filtersForm) {
                filtersForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetchReport();
                });

                filtersForm.querySelectorAll('.users-filter-date, .users-select').forEach(function (input) {
                    input.addEventListener('change', fetchReport);
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    filtersForm.date.value = new Date().toISOString().slice(0, 10);
                    if (filtersForm.branch_id) filtersForm.branch_id.value = '';
                    fetchReport();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
