@extends('admin.layouts.master')

@section('page-title')
    فاتورة البيع {{ $saleInvoice->number }}
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
                    <h5 class="users-page-title">فاتورة البيع: {{ $saleInvoice->number }}</h5>
                    <div class="users-header-actions">
                        @if ($saleInvoice->status === \App\Models\SaleInvoice::STATUS_DRAFT)
                            @can('sale-invoice-confirm')
                                <form action="{{ route('admin.sale-invoices.confirm', $saleInvoice) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('تأكيد الفاتورة سينشئ حركات صرف مخزون. متابعة؟');">
                                    @csrf
                                    <button type="submit" class="users-btn-submit" style="padding: 0.5rem 1rem;">
                                        <i class="fas fa-check"></i> تأكيد الفاتورة
                                    </button>
                                </form>
                            @endcan
                            @can('sale-invoice-edit')
                                <a href="{{ route('admin.sale-invoices.edit', $saleInvoice) }}" class="users-btn-secondary">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                            @endcan
                            @can('sale-invoice-delete')
                                <form action="{{ route('admin.sale-invoices.destroy', $saleInvoice) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف الفاتورة؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="users-action-btn users-action-btn--delete" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        @endif
                        <a href="{{ route('admin.sale-invoices.print', $saleInvoice) }}" target="_blank" class="users-btn-secondary">
                            <i class="fas fa-print"></i> طباعة
                        </a>
                        @if ($saleInvoice->status === \App\Models\SaleInvoice::STATUS_CONFIRMED)
                            <a href="{{ route('admin.sale-returns.create', ['sale_invoice_id' => $saleInvoice->id]) }}" class="users-btn-secondary">
                                <i class="fas fa-undo"></i> مرتجع
                            </a>
                        @endif
                        <a href="{{ route('admin.sale-invoices.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-info-circle"></i> بيانات الفاتورة</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-building"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الفرع</span>
                                        <div class="users-detail-item__value">{{ $saleInvoice->branch->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">العميل</span>
                                        <div class="users-detail-item__value">{{ $saleInvoice->customer->name ?? 'عميل نقدي' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-warehouse"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">مخزن الصرف</span>
                                        <div class="users-detail-item__value">{{ $saleInvoice->warehouse->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التاريخ</span>
                                        <div class="users-detail-item__value">{{ $saleInvoice->invoice_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($saleInvoice->status === 'draft')
                                                <span class="users-badge users-badge--role" style="background: rgba(107, 114, 128, 0.15); color: #4b5563;">مسودة</span>
                                            @elseif ($saleInvoice->status === 'confirmed')
                                                <span class="users-badge users-badge--active">مؤكدة</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">ملغاة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-credit-card"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">حالة الدفع</span>
                                        <div class="users-detail-item__value">
                                            @if ($saleInvoice->payment_status === 'paid')
                                                <span class="users-badge users-badge--active">مدفوع</span>
                                            @elseif ($saleInvoice->payment_status === 'partial')
                                                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">جزئي</span>
                                            @else
                                                <span class="users-badge users-badge--role">معلق</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-user-tie"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المستخدم</span>
                                        <div class="users-detail-item__value">{{ $saleInvoice->user->name ?? '—' }}</div>
                                    </div>
                                </div>
                                @if ($saleInvoice->notes)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-sticky-note"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">ملاحظات</span>
                                            <div class="users-detail-item__value">{{ $saleInvoice->notes }}</div>
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
                                        <span class="users-detail-item__label">المجموع الفرعي</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($saleInvoice->subtotal, 2) }}</span></div>
                                    </div>
                                </div>
                                @if ($saleInvoice->discount_amount > 0)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-tag"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">الخصم@if ($saleInvoice->coupon) (كوبون {{ $saleInvoice->coupon->code }})@endif</span>
                                            <div class="users-detail-item__value"><span class="users-amount users-qty--out">- {{ number_format($saleInvoice->discount_amount, 2) }}</span></div>
                                        </div>
                                    </div>
                                @endif
                                @if ($saleInvoice->tax_amount > 0)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-percent"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">الضريبة ({{ $saleInvoice->tax_rate }}%)</span>
                                            <div class="users-detail-item__value"><span class="users-amount">{{ number_format($saleInvoice->tax_amount, 2) }}</span></div>
                                        </div>
                                    </div>
                                @endif
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-money-bill-wave"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الإجمالي</span>
                                        <div class="users-detail-item__value"><span class="users-amount" style="font-size: 1rem;">{{ number_format($saleInvoice->total, 2) }}</span></div>
                                    </div>
                                </div>
                                @if ($saleInvoice->status === \App\Models\SaleInvoice::STATUS_CONFIRMED)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-check-circle"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">المدفوع</span>
                                            <div class="users-detail-item__value"><span class="users-amount users-qty--in">{{ number_format($saleInvoice->total_paid, 2) }}</span></div>
                                        </div>
                                    </div>
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-hourglass-half"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">المتبقي</span>
                                            <div class="users-detail-item__value"><span class="users-amount">{{ number_format($saleInvoice->remaining_amount, 2) }}</span></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-table-card" style="margin-top: 1.25rem;">
                    <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-list"></i> بنود الفاتورة</h6>
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
                                @forelse ($saleInvoice->items as $item)
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

                @if ($saleInvoice->status === \App\Models\SaleInvoice::STATUS_CONFIRMED && $paymentMethods->isNotEmpty())
                    <div class="users-form-card" style="margin-top: 1.25rem;">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-wallet"></i> تسجيل دفعة جديدة</h6>
                        </div>
                        <form action="{{ route('admin.sale-invoices.payments.store', $saleInvoice) }}" method="POST" class="users-form-card__body">
                            @csrf
                            <div class="users-form-grid">
                                <div class="users-form-group">
                                    <label class="users-form-label"><i class="fas fa-coins"></i> المبلغ <span class="users-form-required">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="users-form-input"
                                        value="{{ number_format($saleInvoice->remaining_amount, 2, '.', '') }}" max="{{ $saleInvoice->remaining_amount }}" required>
                                </div>
                                <div class="users-form-group">
                                    <label class="users-form-label"><i class="fas fa-credit-card"></i> طريقة الدفع <span class="users-form-required">*</span></label>
                                    <select name="payment_method_id" class="users-form-select" required>
                                        @foreach ($paymentMethods as $pm)
                                            <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if (isset($treasuries) && $treasuries->isNotEmpty())
                                    <div class="users-form-group">
                                        <label class="users-form-label"><i class="fas fa-university"></i> خزنة / بنك</label>
                                        <select name="treasury_id" class="users-form-select">
                                            <option value="">— اختياري —</option>
                                            @foreach ($treasuries as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="users-form-group">
                                    <label class="users-form-label"><i class="fas fa-calendar"></i> تاريخ الدفع <span class="users-form-required">*</span></label>
                                    <input type="date" name="payment_date" class="users-form-input" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="users-form-group">
                                    <label class="users-form-label"><i class="fas fa-hashtag"></i> مرجع (اختياري)</label>
                                    <input type="text" name="reference" class="users-form-input" placeholder="رقم شيك / تحويل">
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                                    <input type="text" name="notes" class="users-form-input">
                                </div>
                            </div>
                            <div class="users-form-actions" style="margin-top: 1rem; padding-top: 0;">
                                <button type="submit" class="users-btn-submit"><i class="fas fa-plus"></i> تسجيل الدفعة</button>
                            </div>
                        </form>
                    </div>
                @endif

                @if ($saleInvoice->status === \App\Models\SaleInvoice::STATUS_CONFIRMED && $saleInvoice->customer && (int) $saleInvoice->customer->loyalty_points > 0 && $saleInvoice->remaining_amount > 0)
                    <div class="users-form-card" style="margin-top: 1.25rem; border-color: var(--users-primary);">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-gift"></i> استبدال نقاط الولاء</h6>
                        </div>
                        <div class="users-form-card__body">
                            <p class="users-muted-text" style="margin-bottom: 1rem;">
                                رصيد العميل: <strong>{{ $saleInvoice->customer->loyalty_points }}</strong> نقطة —
                                المتبقي على الفاتورة: <strong>{{ number_format($saleInvoice->remaining_amount, 2) }}</strong>
                            </p>
                            <form action="{{ route('admin.sale-invoices.redeem-points', $saleInvoice) }}" method="POST" class="users-form-grid" style="align-items: end;">
                                @csrf
                                <div class="users-form-group">
                                    <label class="users-form-label">عدد النقاط <span class="users-form-required">*</span></label>
                                    <input type="number" name="points" class="users-form-input" min="1" max="{{ $saleInvoice->customer->loyalty_points }}" value="1" required>
                                </div>
                                <div class="users-form-group">
                                    <button type="submit" class="users-btn-secondary"><i class="fas fa-exchange-alt"></i> استبدال النقاط</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($saleInvoice->payments->isNotEmpty())
                    <div class="users-table-card" style="margin-top: 1.25rem;">
                        <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                            <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-receipt"></i> الدفعات</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الطريقة</th>
                                        <th>خزنة/بنك</th>
                                        <th>المبلغ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($saleInvoice->payments as $pay)
                                        <tr>
                                            <td>{{ $pay->payment_date->format('Y-m-d') }}</td>
                                            <td>{{ $pay->paymentMethod->name ?? '—' }}</td>
                                            <td>{{ $pay->treasury->name ?? '—' }}</td>
                                            <td><span class="users-amount users-qty--in">{{ number_format($pay->amount, 2) }}</span></td>
                                            <td>
                                                <form action="{{ route('admin.sale-invoices.payments.destroy', [$saleInvoice, $pay]) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('حذف هذه الدفعة؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="users-action-btn users-action-btn--delete" title="حذف"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
