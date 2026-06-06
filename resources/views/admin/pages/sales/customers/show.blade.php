@extends('admin.layouts.master')

@section('page-title')
    تفاصيل العميل
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
                    <h5 class="users-page-title">تفاصيل العميل: {{ $customer->name }}</h5>
                    <div class="users-header-actions">
                        @can('customer-edit')
                            <a href="{{ route('admin.customers.edit', $customer) }}" class="users-btn-edit">
                                <i class="fas fa-edit"></i>
                                تعديل
                            </a>
                        @endcan
                        @can('customer-show')
                            <a href="{{ route('admin.customers.statement', $customer) }}" class="users-btn-secondary">
                                <i class="fas fa-file-invoice"></i>
                                كشف حساب
                            </a>
                        @endcan
                        <a href="{{ route('admin.customers.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-user-tie"></i>
                                بيانات العميل
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-profile">
                                <div class="users-avatar"><i class="fas fa-user-tie"></i></div>
                                <div>
                                    <h6 class="users-detail-profile__name">{{ $customer->name }}</h6>
                                    @if ($customer->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}"
                                            target="_blank" class="users-phone-cell" title="فتح WhatsApp">
                                            <i class="fab fa-whatsapp"></i>
                                            <span dir="ltr">{{ $customer->phone }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-envelope"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">البريد</span>
                                        <div class="users-detail-item__value">
                                            @if ($customer->email)
                                                <div class="users-email-cell">
                                                    <a href="mailto:{{ $customer->email }}" class="users-email-link">{{ $customer->email }}</a>
                                                    <button type="button" class="users-copy-btn" data-copy="{{ $customer->email }}" title="نسخ البريد">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">العنوان</span>
                                        <div class="users-detail-item__value">{{ $customer->address ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-wallet"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رصيد افتتاحي</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount">{{ number_format($customer->opening_balance, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-layer-group"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">شريحة العملاء</span>
                                        <div class="users-detail-item__value">
                                            @if ($customer->segment)
                                                <span class="users-badge users-badge--role">{{ $customer->segment->name }}</span>
                                            @else
                                                <span class="users-muted-text">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-star"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">نقاط الولاء</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-badge users-badge--role">{{ number_format($customer->loyalty_points) }} نقطة</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-shopping-cart"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المبيعات</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount users-qty--in">{{ number_format($customer->total_sales, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-undo"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المرتجعات</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount users-qty--out">{{ number_format($customer->total_returns, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-hand-holding-usd"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المدفوعات</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount">{{ number_format($customer->total_paid, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-balance-scale"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الرصيد المستحق</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount {{ $customer->balance > 0 ? 'users-qty--out' : '' }}">
                                                <strong>{{ number_format($customer->balance, 2) }}</strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-toggle-on"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($customer->is_active)
                                                <span class="users-badge users-badge--active">نشط</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">غير نشط</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($customer->notes)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-sticky-note"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">ملاحظات</span>
                                            <div class="users-detail-item__value">{{ $customer->notes }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="users-table-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-file-invoice"></i>
                                آخر الفواتير
                            </h6>
                            <a href="{{ route('admin.sale-invoices.index', ['customer_id' => $customer->id]) }}" class="users-btn-secondary">
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
                                        <th style="min-width: 80px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($customer->saleInvoices as $inv)
                                        <tr>
                                            <td>
                                                <span class="users-badge users-badge--role" dir="ltr">{{ $inv->number }}</span>
                                            </td>
                                            <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                                            <td>
                                                <span class="users-amount">{{ number_format($inv->total, 2) }}</span>
                                            </td>
                                            <td>
                                                @if ($inv->payment_status === 'paid')
                                                    <span class="users-badge users-badge--active">مدفوع</span>
                                                @elseif ($inv->payment_status === 'partial')
                                                    <span class="users-badge users-badge--role">جزئي</span>
                                                @else
                                                    <span class="users-badge users-badge--inactive">معلق</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="users-actions">
                                                    <a class="users-action-btn users-action-btn--view"
                                                        href="{{ route('admin.sale-invoices.show', $inv) }}"
                                                        title="عرض الفاتورة">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="users-empty">لا توجد فواتير لهذا العميل.</td>
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
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>
        AdminPremium.initCopyButtons('.users-premium');
    </script>
@stop
