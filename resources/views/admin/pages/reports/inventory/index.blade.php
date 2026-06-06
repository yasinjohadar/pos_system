@extends('admin.layouts.master')

@section('page-title')
    تقرير المخزون
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
                    <h5 class="users-page-title">تقرير المخزون الحالي</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.reports.inventory.reorder') }}" class="users-btn-secondary">
                            <i class="fas fa-bell"></i> تنبيهات إعادة الطلب
                        </a>
                        <a href="{{ route('admin.reports.inventory.index', array_merge(request()->only(['warehouse_id', 'category_id']), ['format' => 'csv'])) }}"
                            class="users-btn-secondary" id="inventory-report-export">
                            <i class="fas fa-file-csv"></i> تصدير CSV
                        </a>
                    </div>
                </div>

                <div class="users-filters-card">
                    <form id="inventory-report-filters" action="{{ route('admin.reports.inventory.index') }}" method="GET" class="users-filters-form">
                        <select name="warehouse_id" class="users-select" title="المخزن">
                            <option value="">جميع المخازن</option>
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ (string) $warehouseId === (string) $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>

                        <select name="category_id" class="users-select" title="التصنيف">
                            <option value="">جميع التصنيفات</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="inventory-report-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="inventory-report-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">المنتج</th>
                                    <th>التصنيف</th>
                                    <th style="min-width: 160px;">المخزن</th>
                                    <th>الكمية</th>
                                </tr>
                            </thead>
                            <tbody id="inventory-report-body">
                                @include('admin.pages.reports.inventory.partials.table-rows')
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
            var filtersForm = document.getElementById('inventory-report-filters');
            var tableBody = document.getElementById('inventory-report-body');
            var tableCard = document.getElementById('inventory-report-card');
            var clearBtn = document.getElementById('inventory-report-clear');
            var exportLink = document.getElementById('inventory-report-export');
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

                filtersForm.querySelectorAll('.users-select').forEach(function (input) {
                    input.addEventListener('change', fetchReport);
                });
            }

            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    if (filtersForm.warehouse_id) filtersForm.warehouse_id.value = '';
                    if (filtersForm.category_id) filtersForm.category_id.value = '';
                    fetchReport();
                });
            }

            updateExportLink();
        })();
    </script>
@stop
