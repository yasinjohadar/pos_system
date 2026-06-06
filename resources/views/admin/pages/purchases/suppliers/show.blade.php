@extends('admin.layouts.master')

@section('page-title')
    تفاصيل المورد
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
                    <h5 class="users-page-title">تفاصيل المورد: {{ $supplier->name }}</h5>
                    <div class="users-header-actions">
                        @can('supplier-edit')
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="users-btn-secondary">
                                <i class="fas fa-edit"></i> تعديل
                            </a>
                        @endcan
                        @can('supplier-show')
                            <a href="{{ route('admin.suppliers.statement', $supplier) }}" class="users-btn-secondary">
                                <i class="fas fa-file-invoice"></i> كشف حساب
                            </a>
                        @endcan
                        <a href="{{ route('admin.suppliers.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-truck"></i> بيانات المورد</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-font"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الاسم</span>
                                        <div class="users-detail-item__value">{{ $supplier->name }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-phone"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الهاتف</span>
                                        <div class="users-detail-item__value">{{ $supplier->phone ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-envelope"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">البريد</span>
                                        <div class="users-detail-item__value">{{ $supplier->email ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">العنوان</span>
                                        <div class="users-detail-item__value">{{ $supplier->address ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($supplier->is_active)
                                                <span class="users-badge users-badge--active">نشط</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">غير نشط</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($supplier->notes)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-sticky-note"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">ملاحظات</span>
                                            <div class="users-detail-item__value">{{ $supplier->notes }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-calculator"></i> الملخص المالي</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-coins"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رصيد افتتاحي</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($supplier->opening_balance, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-shopping-cart"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المشتريات</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($supplier->total_purchases, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-undo"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المرتجعات</span>
                                        <div class="users-detail-item__value"><span class="users-amount users-qty--out">{{ number_format($supplier->total_returns, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-wallet"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المدفوعات</span>
                                        <div class="users-detail-item__value"><span class="users-amount users-qty--in">{{ number_format($supplier->total_paid, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-balance-scale"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الرصيد المستحق للمورد</span>
                                        <div class="users-detail-item__value"><span class="users-amount" style="font-size: 1rem;">{{ number_format($supplier->balance, 2) }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-table-card" style="margin-top: 1.25rem;">
                    <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border); display: flex; justify-content: space-between; align-items: center;">
                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-file-invoice"></i> آخر فواتير الشراء</h6>
                        <a href="{{ route('admin.purchase-invoices.index', ['supplier_id' => $supplier->id]) }}" class="users-btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8125rem;">
                            عرض الكل
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>رقم الفاتورة</th>
                                    <th>التاريخ</th>
                                    <th>الإجمالي</th>
                                    <th>حالة الدفع</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($supplier->purchaseInvoices as $inv)
                                    <tr>
                                        <td><span class="users-badge users-badge--role">{{ $inv->number }}</span></td>
                                        <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                                        <td><span class="users-amount">{{ number_format($inv->total, 2) }}</span></td>
                                        <td>
                                            @if ($inv->payment_status === 'paid')
                                                <span class="users-badge users-badge--active">مدفوع</span>
                                            @elseif ($inv->payment_status === 'partial')
                                                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">جزئي</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">معلق</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="users-action-btn users-action-btn--view"
                                                href="{{ route('admin.purchase-invoices.show', $inv) }}"
                                                title="عرض الفاتورة">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="users-empty">لا توجد فواتير شراء لهذا المورد</td>
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

@section('script')
    @include('admin.components.premium.scripts')
@stop
