@extends('admin.layouts.master')

@section('page-title')
    كشف حساب المورد: {{ $supplier->name }}
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">كشف حساب المورد: {{ $supplier->name }}</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.suppliers.show', $supplier) }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-filters-card">
                    <form action="{{ route('admin.suppliers.statement', $supplier) }}" method="GET" class="users-filters-form">
                        <input type="date" name="as_of_date" class="users-search-input" style="max-width: 220px;"
                            value="{{ $asOfDate?->format('Y-m-d') }}" placeholder="حتى تاريخ">

                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>

                        @if ($asOfDate)
                            <a href="{{ route('admin.suppliers.statement', $supplier) }}" class="users-btn-filter users-btn-filter--clear">
                                <i class="fas fa-times me-1"></i> عرض الكل
                            </a>
                        @endif

                        <span class="users-badge users-badge--role" style="margin-right: auto;">
                            رصيد افتتاحي: {{ number_format($supplier->opening_balance, 2) }}
                        </span>
                    </form>
                </div>

                <div class="users-table-card">
                    <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-list"></i> حركات الحساب</h6>
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
                                                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">مرتجع</span>
                                            @else
                                                <span class="users-badge users-badge--active">دفعة</span>
                                            @endif
                                        </td>
                                        <td>{{ $e['reference'] }}</td>
                                        <td>{{ $e['description'] }}</td>
                                        <td>
                                            @if ($e['debit'] > 0)
                                                <span class="users-amount">{{ number_format($e['debit'], 2) }}</span>
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
                                        <td><span class="users-amount">{{ number_format($e['balance'], 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="users-empty">
                                            لا توجد حركات لهذا المورد{{ $asOfDate ? ' حتى التاريخ المحدد' : '' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($entries->isNotEmpty())
                        <div class="users-pagination" style="justify-content: flex-end; padding: 1rem 1.25rem;">
                            <strong>الرصيد الحالي:</strong>
                            <span class="users-amount" style="margin-right: 0.5rem;">{{ number_format($entries->last()['balance'], 2) }}</span>
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
