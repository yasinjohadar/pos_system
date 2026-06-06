@extends('admin.layouts.master')

@section('page-title')
    تفاصيل القيد
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    @php
        $refBase = $journalEntry->reference_type ? class_basename($journalEntry->reference_type) : null;
        $referenceLabels = [
            'SaleInvoice' => 'فاتورة بيع',
            'PurchaseInvoice' => 'فاتورة شراء',
            'CashVoucher' => 'سند قبض/صرف',
            'SaleReturn' => 'مرتجع بيع',
            'PurchaseReturn' => 'مرتجع شراء',
        ];
        $refLabel = $refBase ? ($referenceLabels[$refBase] ?? $refBase) : null;
        $totalDebit = $journalEntry->lines->sum('debit');
        $totalCredit = $journalEntry->lines->sum('credit');
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">قيد: {{ $journalEntry->entry_number }}</h5>
                    <div class="users-header-actions">
                        @if (!$journalEntry->is_posted)
                            @can('journal-entry-post')
                                <form action="{{ route('admin.journal-entries.post', $journalEntry) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="users-btn-create" onclick="return confirm('هل تريد ترحيل هذا القيد؟');">
                                        <i class="fas fa-check"></i> ترحيل
                                    </button>
                                </form>
                            @endcan
                        @elseif ($journalEntry->source !== \App\Models\JournalEntry::SOURCE_REVERSAL)
                            @can('journal-entry-reverse')
                                <form action="{{ route('admin.journal-entries.reverse', $journalEntry) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="users-btn-secondary" onclick="return confirm('هل تريد إنشاء قيد عكسي؟');">
                                        <i class="fas fa-undo"></i> عكس القيد
                                    </button>
                                </form>
                            @endcan
                        @endif
                        <a href="{{ route('admin.journal-entries.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-info-circle"></i> بيانات القيد</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-hashtag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رقم القيد</span>
                                        <div class="users-detail-item__value" dir="ltr">{{ $journalEntry->entry_number }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التاريخ</span>
                                        <div class="users-detail-item__value">{{ $journalEntry->entry_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-align-right"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الوصف</span>
                                        <div class="users-detail-item__value">{{ $journalEntry->description ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-link"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المرجع</span>
                                        <div class="users-detail-item__value">
                                            @if ($refLabel)
                                                <span class="users-badge users-badge--role">{{ $refLabel }} #{{ $journalEntry->reference_id }}</span>
                                            @else
                                                —
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">أنشئ بواسطة</span>
                                        <div class="users-detail-item__value">{{ $journalEntry->createdBy->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-flag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($journalEntry->is_posted)
                                                <span class="users-badge users-badge--active">مرحّل</span>
                                            @else
                                                <span class="users-badge users-badge--role">مسودة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
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
                                    <div class="users-detail-item__icon"><i class="fas fa-arrow-down"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي المدين</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount users-qty--in">{{ number_format($totalDebit, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-arrow-up"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">إجمالي الدائن</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount users-qty--out">{{ number_format($totalCredit, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-table-card" style="margin-top: 1.25rem;">
                    <div class="users-form-card__header" style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-list"></i> تفاصيل القيد</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>الحساب</th>
                                    <th>الكود</th>
                                    <th>مدين</th>
                                    <th>دائن</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($journalEntry->lines as $line)
                                    <tr>
                                        <td>
                                            <div class="users-user-cell">
                                                <div class="users-avatar"><i class="fas fa-book"></i></div>
                                                <span class="users-user-name" style="cursor: default;">{{ $line->account->name ?? '—' }}</span>
                                            </div>
                                        </td>
                                        <td><span class="users-badge users-badge--role" dir="ltr">{{ $line->account->code ?? '—' }}</span></td>
                                        <td>
                                            @if ((float) $line->debit > 0)
                                                <span class="users-amount users-qty--in">{{ number_format($line->debit, 2) }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if ((float) $line->credit > 0)
                                                <span class="users-amount users-qty--out">{{ number_format($line->credit, 2) }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $line->description ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="users-empty">لا توجد بنود</td>
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
