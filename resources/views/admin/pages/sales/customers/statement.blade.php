@extends('admin.layouts.master')

@section('page-title')
    كشف حساب العميل: {{ $customer->name }}
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
                    <h5 class="users-page-title">كشف حساب العميل: {{ $customer->name }}</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.customers.show', $customer) }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-filters-card">
                    <form method="GET" action="{{ route('admin.customers.statement', $customer) }}" class="users-filters-form">
                        <input type="date" name="as_of_date" class="users-search-input users-filter-date"
                            value="{{ $asOfDate?->format('Y-m-d') }}" title="رصيد حتى تاريخ">
                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                        @if ($asOfDate)
                            <a href="{{ route('admin.customers.statement', $customer) }}" class="users-btn-filter users-btn-filter--clear">
                                <i class="fas fa-times me-1"></i> عرض الكل
                            </a>
                        @endif
                    </form>
                </div>

                <div class="users-table-card">
                    <div class="users-detail-card__header">
                        <h6 class="users-detail-card__title">
                            <i class="fas fa-list-alt"></i>
                            حركات الحساب
                        </h6>
                        <span class="users-badge users-badge--role">
                            رصيد افتتاحي: {{ number_format($customer->opening_balance, 2) }}
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>النوع</th>
                                    <th>المرجع</th>
                                    <th>البيان</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>الرصيد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($entries as $e)
                                    <tr>
                                        <td>{{ $e['date']->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($e['type'] === 'invoice')
                                                <span class="users-badge users-badge--role">فاتورة</span>
                                            @elseif ($e['type'] === 'return')
                                                <span class="users-badge users-badge--inactive">مرتجع</span>
                                            @else
                                                <span class="users-badge users-badge--active">دفعة</span>
                                            @endif
                                        </td>
                                        <td dir="ltr">{{ $e['reference'] }}</td>
                                        <td>{{ $e['description'] }}</td>
                                        <td>
                                            @if ($e['debit'] > 0)
                                                <span class="users-amount users-qty--out">{{ number_format($e['debit'], 2) }}</span>
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($e['credit'] > 0)
                                                <span class="users-amount users-qty--in">{{ number_format($e['credit'], 2) }}</span>
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="users-amount"><strong>{{ number_format($e['balance'], 2) }}</strong></span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="users-empty">
                                            لا توجد حركات لهذا العميل{{ $asOfDate ? ' حتى التاريخ المحدد' : '' }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($entries->isNotEmpty())
                        <div class="users-detail-card__header" style="border-top: 1px solid var(--users-border); border-bottom: 0;">
                            <span><strong>الرصيد الحالي:</strong></span>
                            <span class="users-amount users-qty--out">{{ number_format($entries->last()['balance'], 2) }}</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
