@extends('admin.layouts.master')

@section('page-title')
    تقرير أعمار ذمم الموردين
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
                    <h5 class="users-page-title">تقرير أعمار ذمم الموردين</h5>
                    <a href="{{ route('admin.reports.suppliers.aging', array_merge(request()->only(['supplier_id', 'as_of_date']), ['format' => 'csv'])) }}"
                        class="users-btn-secondary" id="suppliers-aging-export">
                        <i class="fas fa-file-csv"></i> تصدير CSV
                    </a>
                </div>

                <div class="users-filters-card">
                    <form id="suppliers-aging-filters" action="{{ route('admin.reports.suppliers.aging') }}" method="GET" class="users-filters-form">
                        <div style="min-width: 240px; flex: 1;">
                            @include('admin.components.premium.supplier-select', [
                                'name' => 'supplier_id',
                                'id' => 'supplier_id',
                                'selected' => $supplier ?? null,
                                'placeholder' => 'اختر المورد',
                                'required' => false,
                            ])
                        </div>

                        <input type="date" name="as_of_date" class="users-search-input users-filter-date"
                            value="{{ $asOfDate->format('Y-m-d') }}" title="حتى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="suppliers-aging-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div id="suppliers-aging-card" class="users-table-card--loading-target" style="background: transparent; border: none; box-shadow: none; padding: 0;">
                    <div id="suppliers-aging-summary">
                        @include('admin.pages.reports.suppliers.partials.aging-summary')
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
            var filtersForm = document.getElementById('suppliers-aging-filters');
            var summaryEl = document.getElementById('suppliers-aging-summary');
            var reportCard = document.getElementById('suppliers-aging-card');
            var clearBtn = document.getElementById('suppliers-aging-clear');
            var exportLink = document.getElementById('suppliers-aging-export');
            var isLoading = false;

            function getParams() {
                return new URLSearchParams(new FormData(filtersForm));
            }

            function updateExportLink() {
                if (!exportLink) return;
                var params = getParams();
                if (!params.get('supplier_id')) {
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
                if (!params.get('supplier_id')) {
                    AdminPremium.showToast('يرجى اختيار مورد', 'error');
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

            AdminPremium.initSupplierSearch({
                url: '{{ route('admin.suppliers.search-select') }}',
                selector: '#supplier_id',
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
                        if (filtersForm.supplier_id && filtersForm.supplier_id.value) {
                            fetchReport();
                        }
                    });
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    if (typeof jQuery !== 'undefined' && jQuery('#supplier_id').data('select2')) {
                        jQuery('#supplier_id').val(null).trigger('change');
                    } else if (filtersForm.supplier_id) {
                        filtersForm.supplier_id.value = '';
                    }
                    filtersForm.as_of_date.value = new Date().toISOString().slice(0, 10);
                    summaryEl.innerHTML = '<div class="users-table-card"><div class="users-empty" style="padding: 2.5rem 1rem;">اختر مورداً واضغط «عرض» — جرّب <strong>مورد أعمار الذمم — تجريبي</strong> بعد تشغيل <code>PurchaseSeeder</code></div></div>';
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
