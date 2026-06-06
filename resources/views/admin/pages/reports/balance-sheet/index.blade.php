@extends('admin.layouts.master')

@section('page-title')
    الميزانية العمومية
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    @php
        $typeLabels = [
            'asset' => 'الأصول',
            'liability' => 'الخصوم',
            'equity' => 'حقوق الملكية',
        ];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">الميزانية العمومية</h5>
                </div>

                <div class="users-filters-card">
                    <form action="{{ route('admin.reports.balance-sheet.index') }}" method="GET" class="users-filters-form">
                        <label class="users-form-label mb-0">حتى تاريخ:</label>
                        <input type="date" name="as_of" class="users-search-input users-filter-date" value="{{ $asOf }}">
                        <button type="submit" class="users-btn-filter users-btn-filter--search">
                            <i class="fas fa-filter me-1"></i> عرض
                        </button>
                    </form>
                </div>

                <div class="users-detail-grid" style="margin-bottom: 1.25rem;">
                    <div class="users-detail-card">
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي الأصول</span>
                                        <div class="users-detail-item__value"><span class="users-amount users-qty--in">{{ number_format($totalAssets, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي الخصوم</span>
                                        <div class="users-detail-item__value"><span class="users-amount users-qty--out">{{ number_format($totalLiabilities, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">حقوق الملكية</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($totalEquity, 2) }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @foreach (['asset', 'liability', 'equity'] as $type)
                    <div class="users-table-card" style="margin-bottom: 1rem;">
                        <div class="users-form-card__header" style="padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                            <h6 class="users-form-card__title" style="margin: 0;">{{ $typeLabels[$type] ?? $type }}</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>الكود</th>
                                        <th>اسم الحساب</th>
                                        <th>الرصيد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($balances[$type] ?? [] as $item)
                                        <tr>
                                            <td><span class="users-badge users-badge--role" dir="ltr">{{ $item->code }}</span></td>
                                            <td>{{ $item->name }}</td>
                                            <td><span class="users-amount">{{ number_format($item->balance, 2) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="users-empty">لا توجد حسابات</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
