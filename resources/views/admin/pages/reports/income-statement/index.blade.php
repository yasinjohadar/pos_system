@extends('admin.layouts.master')

@section('page-title')
    قائمة الدخل
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
                    <h5 class="users-page-title">قائمة الدخل</h5>
                    <a href="{{ route('admin.reports.income-statement.index', array_merge(request()->only(['from', 'to']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="income-statement-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="income-statement-filters" action="{{ route('admin.reports.income-statement.index') }}" method="GET" class="users-filters-form">
                        <input type="date" name="from" class="users-search-input users-filter-date"
                            value="{{ $from }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date"
                            value="{{ $to }}" title="إلى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="income-statement-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div id="income-statement-card" class="users-table-card users-table-card--loading-target" style="padding: 0; background: transparent; border: none; box-shadow: none;">
                    <div id="income-statement-summary">
                        @include('admin.pages.reports.income-statement.partials.summary')
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
            var filtersForm = document.getElementById('income-statement-filters');
            var summaryEl = document.getElementById('income-statement-summary');
            var reportCard = document.getElementById('income-statement-card');
            var clearBtn = document.getElementById('income-statement-clear');
            var exportLink = document.getElementById('income-statement-export');
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
