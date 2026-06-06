@extends('admin.layouts.master')

@section('page-title')
    تقرير الشرائح
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>
        .users-report-kpi-grid--3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }
        @media (max-width: 991px) {
            .users-report-kpi-grid--3 { grid-template-columns: 1fr; }
        }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">تقرير الشرائح</h5>
                    <div class="d-flex flex-wrap gap-2">
                        @can('customer-segment-list')
                            <a href="{{ route('admin.customer-segments.index') }}" class="users-btn-secondary">
                                <i class="fas fa-cog"></i> إدارة الشرائح
                            </a>
                        @endcan
                        <a href="{{ route('admin.reports.segments.index', ['format' => 'csv']) }}"
                            class="users-btn-secondary" id="segments-report-export">
                            <i class="fas fa-file-csv"></i> تصدير CSV
                        </a>
                        <button type="button" class="users-btn-filter users-btn-filter--search" id="segments-report-refresh">
                            <i class="fas fa-sync-alt me-1"></i> تحديث
                        </button>
                    </div>
                </div>

                <div id="segments-report-card" class="users-table-card--loading-target" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                    <div id="segments-report-summary">
                        @include('admin.pages.reports.segments.partials.summary')
                    </div>

                    <div class="users-table-card" id="segments-table-card">
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 160px;">الشريحة</th>
                                        <th>عدد العملاء</th>
                                        <th>إجمالي المبيعات</th>
                                        <th>متوسط الرصيد</th>
                                        <th>عدد الفواتير</th>
                                        <th>متوسط قيمة الفاتورة</th>
                                    </tr>
                                </thead>
                                <tbody id="segments-report-body">
                                    @include('admin.pages.reports.segments.partials.table-rows')
                                </tbody>
                            </table>
                        </div>
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
            var reportUrl = '{{ route('admin.reports.segments.index') }}';
            var summaryEl = document.getElementById('segments-report-summary');
            var tableBody = document.getElementById('segments-report-body');
            var reportCard = document.getElementById('segments-report-card');
            var tableCard = document.getElementById('segments-table-card');
            var refreshBtn = document.getElementById('segments-report-refresh');
            var isLoading = false;

            function fetchReport() {
                if (!summaryEl || !tableBody || isLoading) return;

                isLoading = true;
                if (reportCard) reportCard.classList.add('users-table-card--loading');

                fetch(reportUrl, {
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
                        tableBody.innerHTML = data.tbody;
                    })
                    .catch(function () {
                        AdminPremium.showToast('حدث خطأ أثناء تحميل التقرير', 'error');
                    })
                    .finally(function () {
                        isLoading = false;
                        if (reportCard) reportCard.classList.remove('users-table-card--loading');
                    });
            }

            if (refreshBtn) {
                refreshBtn.addEventListener('click', fetchReport);
            }
        })();
    </script>
@stop
