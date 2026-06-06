@extends('admin.layouts.master')

@section('page-title')
    دفتر الأستاذ العام
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
                    <h5 class="users-page-title">دفتر الأستاذ العام</h5>
                </div>

                <div class="users-filters-card">
                    <form id="general-ledger-filters" action="{{ route('admin.reports.general-ledger.index') }}" method="GET" class="users-filters-form">
                        <select name="account_id" class="users-select">
                            <option value="">جميع الحسابات</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}" {{ (string) $accountId === (string) $acc->id ? 'selected' : '' }}>
                                    {{ $acc->code }} — {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                        <input type="date" name="from" class="users-search-input users-filter-date" value="{{ $from }}" title="من تاريخ">
                        <input type="date" name="to" class="users-search-input users-filter-date" value="{{ $to }}" title="إلى تاريخ">
                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        <button type="button" id="general-ledger-clear" class="users-btn-filter users-btn-filter--clear">
                            <i class="fas fa-times me-1"></i> مسح
                        </button>
                    </form>
                </div>

                <div class="users-table-card" id="general-ledger-card">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>رقم القيد</th>
                                    <th>الحساب</th>
                                    <th>الوصف</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                </tr>
                            </thead>
                            <tbody id="general-ledger-body">
                                @include('admin.pages.reports.general-ledger.partials.table-rows')
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
            var filtersForm = document.getElementById('general-ledger-filters');
            var tableBody = document.getElementById('general-ledger-body');
            var tableCard = document.getElementById('general-ledger-card');
            var clearBtn = document.getElementById('general-ledger-clear');
            var isLoading = false;

            function fetchReport() {
                if (!filtersForm || !tableBody || isLoading) return;
                var params = new URLSearchParams(new FormData(filtersForm));
                isLoading = true;
                if (tableCard) tableCard.classList.add('users-table-card--loading');

                fetch(filtersForm.action + '?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                    .then(function (r) { return r.json(); })
                    .then(function (data) { tableBody.innerHTML = data.tbody; })
                    .catch(function () { AdminPremium.showToast('حدث خطأ أثناء تحميل التقرير', 'error'); })
                    .finally(function () {
                        isLoading = false;
                        if (tableCard) tableCard.classList.remove('users-table-card--loading');
                    });
            }

            if (filtersForm) {
                filtersForm.addEventListener('submit', function (e) { e.preventDefault(); fetchReport(); });
            }
            if (clearBtn && filtersForm) {
                clearBtn.addEventListener('click', function () {
                    filtersForm.account_id.value = '';
                    var today = new Date();
                    filtersForm.from.value = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().slice(0, 10);
                    filtersForm.to.value = today.toISOString().slice(0, 10);
                    fetchReport();
                });
            }
        })();
    </script>
@stop
