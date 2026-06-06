@extends('admin.layouts.master')

@section('page-title')
    مرتجع البيع {{ $saleReturn->return_number }}
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
                    <h5 class="users-page-title">مرتجع البيع: {{ $saleReturn->return_number }}</h5>
                    <div class="users-header-actions">
                        @if ($saleReturn->status === \App\Models\SaleReturn::STATUS_PENDING)
                            @can('sale-return-complete')
                                <form action="{{ route('admin.sale-returns.complete', $saleReturn) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('إكمال المرتجع سيدخل الكميات إلى المخزون. متابعة؟');">
                                    @csrf
                                    <button type="submit" class="users-btn-submit" style="padding: 0.5rem 1rem;">
                                        <i class="fas fa-check"></i> إكمال المرتجع
                                    </button>
                                </form>
                            @endcan
                        @endif
                        <a href="{{ route('admin.sale-invoices.show', $saleReturn->sale_invoice_id) }}" class="users-btn-secondary">
                            <i class="fas fa-file-invoice"></i> الفاتورة الأصلية
                        </a>
                        <a href="{{ route('admin.sale-returns.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-info-circle"></i> بيانات المرتجع</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-receipt"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الفاتورة الأصلية</span>
                                        <div class="users-detail-item__value">
                                            <a href="{{ route('admin.sale-invoices.show', $saleReturn->saleInvoice) }}" class="users-user-name">
                                                {{ $saleReturn->saleInvoice->number ?? '—' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-warehouse"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">مخزن الاستلام</span>
                                        <div class="users-detail-item__value">{{ $saleReturn->warehouse->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">تاريخ المرتجع</span>
                                        <div class="users-detail-item__value">{{ $saleReturn->return_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($saleReturn->status === 'pending')
                                                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">قيد الانتظار</span>
                                            @elseif ($saleReturn->status === 'completed')
                                                <span class="users-badge users-badge--active">مكتمل</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">ملغى</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المستخدم</span>
                                        <div class="users-detail-item__value">{{ $saleReturn->user->name ?? '—' }}</div>
                                    </div>
                                </div>
                                @if ($saleReturn->notes)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-sticky-note"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">ملاحظات</span>
                                            <div class="users-detail-item__value">{{ $saleReturn->notes }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-calculator"></i> الإجماليات</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-coins"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المجموع المرتجع</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($saleReturn->subtotal_refund, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-percent"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الضريبة</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($saleReturn->tax_refund, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-money-bill-wave"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الإجمالي المرتجع</span>
                                        <div class="users-detail-item__value"><span class="users-amount" style="font-size: 1rem;">{{ number_format($saleReturn->total_refund, 2) }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-table-card" style="margin-top: 1.25rem;">
                    <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-list"></i> بنود المرتجع</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>سعر الوحدة</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($saleReturn->items as $item)
                                    <tr>
                                        <td>
                                            <div class="users-user-cell">
                                                <div class="users-avatar"><i class="fas fa-box"></i></div>
                                                <span class="users-user-name" style="cursor: default;">{{ $item->product->name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td><span class="users-badge users-badge--role">{{ $item->quantity }}</span></td>
                                        <td><span class="users-amount">{{ number_format($item->unit_price, 2) }}</span></td>
                                        <td><span class="users-amount">{{ number_format($item->total, 2) }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="users-empty">لا توجد بنود</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop
