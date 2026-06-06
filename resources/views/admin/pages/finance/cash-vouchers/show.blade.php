@extends('admin.layouts.master')

@section('page-title')
    سند {{ $cashVoucher->voucher_number }}
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
                    <h5 class="users-page-title">
                        سند {{ $cashVoucher->type === 'receipt' ? 'قبض' : 'صرف' }}: {{ $cashVoucher->voucher_number }}
                    </h5>
                    <div class="users-header-actions">
                        @can('cash-voucher-show')
                            <a href="{{ route('admin.cash-vouchers.print', $cashVoucher) }}" class="users-btn-secondary" target="_blank">
                                <i class="fas fa-print"></i> طباعة
                            </a>
                        @endcan
                        <a href="{{ route('admin.cash-vouchers.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-file-invoice"></i> بيانات السند</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-hashtag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رقم السند</span>
                                        <div class="users-detail-item__value" dir="ltr">{{ $cashVoucher->voucher_number }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التاريخ</span>
                                        <div class="users-detail-item__value">{{ $cashVoucher->date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-exchange-alt"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">النوع</span>
                                        <div class="users-detail-item__value">
                                            @if ($cashVoucher->type === 'receipt')
                                                <span class="users-badge users-badge--active">قبض</span>
                                            @else
                                                <span class="users-badge users-badge--inactive">صرف</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-money-bill"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المبلغ</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount {{ $cashVoucher->type === 'receipt' ? 'users-qty--in' : 'users-qty--out' }}">
                                                {{ number_format($cashVoucher->amount, 2) }} {{ $cashVoucher->currency ?? '' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-vault"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الخزنة / البنك</span>
                                        <div class="users-detail-item__value">
                                            @if ($cashVoucher->treasury)
                                                {{ $cashVoucher->treasury->name }}
                                            @elseif ($cashVoucher->bankAccount)
                                                {{ $cashVoucher->bankAccount->name }}
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-tag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الفئة</span>
                                        <div class="users-detail-item__value">{{ $cashVoucher->category ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-align-right"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الوصف</span>
                                        <div class="users-detail-item__value">{{ $cashVoucher->description ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المستخدم</span>
                                        <div class="users-detail-item__value">{{ $cashVoucher->user->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($cashVoucher->isCancelled())
                                                <span class="users-badge users-badge--inactive">ملغى</span>
                                                @if ($cashVoucher->cancelled_at)
                                                    <small class="text-muted d-block">{{ $cashVoucher->cancelled_at->format('Y-m-d H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="users-badge users-badge--active">نشط</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($cashVoucher->notes)
                        <div class="users-detail-card">
                            <div class="users-detail-card__header">
                                <h6 class="users-detail-card__title"><i class="fas fa-sticky-note"></i> ملاحظات</h6>
                            </div>
                            <div class="users-detail-card__body">
                                <p class="mb-0">{{ $cashVoucher->notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($journalEntry)
                    <div class="users-table-card" style="margin-top: 1.25rem;">
                        <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                            <h6 class="users-form-card__title" style="margin: 0;">
                                <i class="fas fa-book"></i> القيد المحاسبي: {{ $journalEntry->entry_number }}
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="users-table">
                                <thead>
                                    <tr>
                                        <th>الحساب</th>
                                        <th>مدين</th>
                                        <th>دائن</th>
                                        <th>الوصف</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($journalEntry->lines as $line)
                                        <tr>
                                            <td>{{ $line->account->name ?? '—' }}</td>
                                            <td>{{ (float) $line->debit > 0 ? number_format($line->debit, 2) : '—' }}</td>
                                            <td>{{ (float) $line->credit > 0 ? number_format($line->credit, 2) : '—' }}</td>
                                            <td>{{ $line->description ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if (!$cashVoucher->isCancelled())
                    @can('cancel_financial_transaction')
                        <div class="users-form-actions" style="margin-top: 1.25rem;">
                            <form action="{{ route('admin.cash-vouchers.cancel', $cashVoucher) }}" method="POST"
                                onsubmit="return confirm('هل أنت متأكد من إلغاء هذا السند؟ سيتم إنشاء قيد عكسي.');">
                                @csrf
                                <button type="submit" class="users-btn-secondary" style="color: var(--users-danger, #dc3545);">
                                    <i class="fas fa-ban"></i> إلغاء السند
                                </button>
                            </form>
                        </div>
                    @endcan
                @endif

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
