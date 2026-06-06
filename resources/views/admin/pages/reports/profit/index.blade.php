@extends('admin.layouts.master')

@section('page-title')
    تقرير الأرباح
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>
        .users-report-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }
        .users-report-kpi-grid--4 {
            grid-template-columns: repeat(4, 1fr);
        }
        @media (max-width: 1199px) {
            .users-report-kpi-grid--4 { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 767px) {
            .users-report-kpi-grid,
            .users-report-kpi-grid--4 { grid-template-columns: 1fr; }
        }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">تقرير الأرباح</h5>
                    <a href="{{ route('admin.reports.profit.index', array_merge(request()->only(['from_date', 'to_date', 'branch_id']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="profit-report-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="profit-report-filters" action="{{ route('admin.reports.profit.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from_date" class="users-search-input users-filter-date"
                            value="{{ $from->format('Y-m-d') }}" title="من تاريخ">
                        <input type="date" name="to_date" class="users-search-input users-filter-date"
                            value="{{ $to->format('Y-m-d') }}" title="إلى تاريخ">

                        <select name="branch_id" class="users-select">
                            <option value="">جميع الفروع</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ (string) $branchId === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="profit-report-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div id="profit-report-card" class="users-table-card--loading-target" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                    <div id="profit-report-summary">
                        @include('admin.pages.reports.profit.partials.summary')
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
            var filtersForm = document.getElementById('profit-report-filters');
            var summaryEl = document.getElementById('profit-report-summary');
            var reportCard = document.getElementById('profit-report-card');
            var clearBtn = document.getElementById('profit-report-clear');
            var exportLink = document.getElementById('profit-report-export');
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
                    var today = new Date();
                    var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                    filtersForm.from_date.value = firstDay.toISOString().slice(0, 10);
                    filtersForm.to_date.value = today.toISOString().slice(0, 10);
                    if (filtersForm.branch_id) filtersForm.branch_id.value = '';
                    fetchReport();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
