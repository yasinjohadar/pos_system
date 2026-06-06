@extends('admin.layouts.master')

@section('page-title')
    تقرير أعمار ديون العملاء
@stop

@section('css')
    @include('admin.components.premium.styles')
    @include('admin.components.premium.product-select-assets')
    <style>
        .users-report-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
        }
        @media (max-width: 1199px) {
            .users-report-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 767px) {
            .users-report-kpi-grid { grid-template-columns: 1fr; }
        }
        .users-filters-form .select2-container {
            min-width: 220px;
        }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">تقرير أعمار ديون العملاء</h5>
                    <a href="{{ route('admin.reports.customers.aging', array_merge(request()->only(['customer_id', 'as_of_date']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="customers-aging-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="customers-aging-filters" action="{{ route('admin.reports.customers.aging') }}" method="GET" class="users-filters-form">
                        <div style="min-width: 240px; flex: 1;">
                            @include('admin.components.premium.customer-select', [
                                'name' => 'customer_id',
                                'id' => 'customer_id',
                                'selected' => $customer ?? null,
                                'placeholder' => 'اختر العميل',
                                'required' => false,
                            ])
                        </div>

                        <input type="date" name="as_of_date" class="users-search-input users-filter-date"
                            value="{{ $asOfDate->format('Y-m-d') }}" title="حتى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="customers-aging-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div id="customers-aging-card" class="users-table-card--loading-target" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                    <div id="customers-aging-summary">
                        @include('admin.pages.reports.customers.partials.aging-summary')
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.product-select-scripts')
    @include('admin.components.premium.scripts')
    <script>
        (function () {
            var filtersForm = document.getElementById('customers-aging-filters');
            var summaryEl = document.getElementById('customers-aging-summary');
            var reportCard = document.getElementById('customers-aging-card');
            var clearBtn = document.getElementById('customers-aging-clear');
            var exportLink = document.getElementById('customers-aging-export');
            var isLoading = false;

            function getParams() {
                return new URLSearchParams(new FormData(filtersForm));
            }

            function updateExportLink() {
                if (!exportLink) return;
                var params = getParams();
                if (!params.get('customer_id')) {
                    exportLink.classList.add('disabled');
                    exportLink.setAttribute('aria-disabled', 'true');
                    return;
                }
                exportLink.classList.remove('disabled');
                exportLink.removeAttribute('aria-disabled');
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
                if (!params.get('customer_id')) {
                    AdminPremium.showToast('يرجى اختيار عميل', 'error');
                    return;
                }

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

            AdminPremium.initCustomerSearch({
                url: '{{ route('admin.customers.search-select') }}',
                selector: '#customer_id',
                minimumInputLength: 0,
                onSelect: function () {
                    fetchReport();
                },
            });

            if (filtersForm) {
                filtersForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    fetchReport();
                });

                filtersForm.querySelectorAll('.users-filter-date').forEach(function (input) {
                    input.addEventListener('change', function () {
                        if (filtersForm.customer_id && filtersForm.customer_id.value) {
                            fetchReport();
                        }
                    });
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    if (typeof jQuery !== 'undefined' && jQuery('#customer_id').data('select2')) {
                        jQuery('#customer_id').val(null).trigger('change');
                    } else if (filtersForm.customer_id) {
                        filtersForm.customer_id.value = '';
                    }
                    filtersForm.as_of_date.value = new Date().toISOString().slice(0, 10);
                    summaryEl.innerHTML = '<div class="users-table-card"><div class="users-empty" style="padding: 2.5rem 1rem;">اختر عميلاً واضغط «عرض» — جرّب <strong>شركة أعمار الديون — تجريبي</strong> بعد تشغيل <code>SalesSeeder</code></div></div>';
                    var url = new URL(window.location.href);
                    url.search = '';
                    window.history.replaceState({}, '', url);
                    updateExportLink();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
