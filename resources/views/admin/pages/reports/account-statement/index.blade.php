@extends('admin.layouts.master')

@section('page-title')
    كشف حساب
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
                    <h5 class="users-page-title">كشف حساب</h5>
                </div>

                <div class="users-filters-card">
                    <form action="{{ route('admin.reports.account-statement.index') }}" method="GET" class="users-filters-form">
                        <select name="account_id" class="users-select" required>
                            <option value="">— اختر حساب —</option>
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
                    </form>
                </div>

                @if ($statement)
                    <div class="users-detail-grid" style="margin-bottom: 1.25rem;">
                        <div class="users-detail-card">
                            <div class="users-detail-card__body">
                                <div class="users-detail-list">
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">الحساب</span>
                                            <div class="users-detail-item__value">{{ $statement['account']->code }} — {{ $statement['account']->name }}</div>
                                        </div>
                                    </div>
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">الرصيد الافتتاحي</span>
                                            <div class="users-detail-item__value">
                                                <span class="users-amount">{{ number_format($statement['opening_balance'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">الرصيد الختامي</span>
                                            <div class="users-detail-item__value">
                                                <span class="users-amount users-qty--in">{{ number_format($statement['closing_balance'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-table-card">
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>رقم القيد</th>
                                        <th>الوصف</th>
                                        <th>مدين</th>
                                        <th>دائن</th>
                                        <th>الرصيد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($statement['rows'] as $row)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($row->entry_date)->format('Y-m-d') }}</td>
                                            <td><span class="users-badge users-badge--role" dir="ltr">{{ $row->entry_number }}</span></td>
                                            <td>{{ $row->description ?? '—' }}</td>
                                            <td>{{ $row->debit > 0 ? number_format($row->debit, 2) : '—' }}</td>
                                            <td>{{ $row->credit > 0 ? number_format($row->credit, 2) : '—' }}</td>
                                            <td><span class="users-amount">{{ number_format($row->balance, 2) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="users-empty">لا توجد حركات</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif ($accountId)
                    <div class="users-table-card"><p class="users-empty p-4 mb-0">لا توجد بيانات للحساب المحدد</p></div>
                @else
                    <div class="users-table-card"><p class="users-empty p-4 mb-0">اختر حساباً لعرض كشف الحساب</p></div>
                @endif

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
