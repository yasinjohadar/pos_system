@extends('admin.layouts.master')

@section('page-title')
    تفاصيل التحويل
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تحويل #{{ $transfer->id }}</h5>
                    <a href="{{ route('admin.stock.transfers.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-info-circle"></i> معلومات التحويل</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التاريخ</span>
                                        <div class="users-detail-item__value">{{ $transfer->transfer_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-warehouse"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">من مخزن</span>
                                        <div class="users-detail-item__value">{{ $transfer->fromWarehouse->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-warehouse"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إلى مخزن</span>
                                        <div class="users-detail-item__value">{{ $transfer->toWarehouse->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($transfer->status === 'completed')
                                                <span class="users-badge users-badge--active">مكتمل</span>
                                            @else
                                                <span class="users-badge users-badge--role">{{ $transfer->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المستخدم</span>
                                        <div class="users-detail-item__value">{{ $transfer->user->name ?? '—' }}</div>
                                    </div>
                                </div>
                                @if ($transfer->notes)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-sticky-note"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">ملاحظات</span>
                                            <div class="users-detail-item__value">{{ $transfer->notes }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-list"></i> بنود التحويل</h6>
                        </div>
                        <div class="users-table-card" style="border: none; box-shadow: none;">
                            @php
                                $items = $transfer->movements->where('type', 'transfer_out')->groupBy('product_id');
                            @endphp
                            <div class="table-responsive">
                                <table class="users-table">
                                    <thead>
                                        <tr>
                                            <th>المنتج</th>
                                            <th>الكمية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($items as $productId => $movements)
                                            <tr>
                                                <td>
                                                    <div class="users-user-cell">
                                                        <div class="users-avatar"><i class="fas fa-box"></i></div>
                                                        <span class="users-user-name" style="cursor: default;">{{ $movements->first()->product->name ?? $productId }}</span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="users-badge users-badge--role">{{ number_format(abs($movements->sum('quantity')), 2) }}</span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="users-empty">لا توجد بنود</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
