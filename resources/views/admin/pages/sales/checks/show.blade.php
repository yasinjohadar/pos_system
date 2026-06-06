@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الشيك: {{ $check->check_number }}
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
                    <h5 class="users-page-title">تفاصيل الشيك: {{ $check->check_number }}</h5>
                    <div class="users-header-actions">
                        @can('check-edit')
                            @if ($check->status === \App\Models\Check::STATUS_UNDER_COLLECTION)
                                <form action="{{ route('admin.checks.update-status', $check) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('تحديث الحالة إلى محصل؟');">
                                    @csrf
                                    <input type="hidden" name="status" value="collected">
                                    <button type="submit" class="users-btn-submit" style="padding: 0.5rem 1rem;">
                                        <i class="fas fa-check"></i> محصل
                                    </button>
                                </form>
                                <form action="{{ route('admin.checks.update-status', $check) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('تحديث الحالة إلى مرتجع؟');">
                                    @csrf
                                    <input type="hidden" name="status" value="returned">
                                    <button type="submit" class="users-action-btn users-action-btn--delete" title="مرتجع">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                            @endif
                        @endcan
                        <a href="{{ route('admin.checks.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-money-check-alt"></i> بيانات الشيك</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-hashtag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رقم الشيك</span>
                                        <div class="users-detail-item__value">{{ $check->check_number }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-coins"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المبلغ</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($check->amount, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-landmark"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">البنك</span>
                                        <div class="users-detail-item__value">
                                            {{ $check->bank_account_id ? ($check->bankAccount->name ?? '—') : ($check->bank_name ?? '—') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">تاريخ الاستحقاق</span>
                                        <div class="users-detail-item__value">{{ $check->due_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($check->status === \App\Models\Check::STATUS_UNDER_COLLECTION)
                                                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">تحت التحصيل</span>
                                            @elseif ($check->status === \App\Models\Check::STATUS_COLLECTED)
                                                <span class="users-badge users-badge--active">محصل</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">مرتجع</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if ($check->salePayment)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-file-invoice"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">مرتبط بفاتورة بيع</span>
                                            <div class="users-detail-item__value">
                                                <a href="{{ route('admin.sale-invoices.show', $check->salePayment->sale_invoice_id) }}" class="users-user-name">
                                                    {{ $check->salePayment->saleInvoice->number ?? '—' }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($check->supplierPayment)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-file-invoice-dollar"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">مرتبط بفاتورة شراء</span>
                                            <div class="users-detail-item__value">
                                                <a href="{{ route('admin.purchase-invoices.show', $check->supplierPayment->purchase_invoice_id) }}" class="users-user-name">
                                                    {{ $check->supplierPayment->purchaseInvoice->number ?? '—' }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if ($check->notes)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-sticky-note"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">ملاحظات</span>
                                            <div class="users-detail-item__value">{{ $check->notes }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
