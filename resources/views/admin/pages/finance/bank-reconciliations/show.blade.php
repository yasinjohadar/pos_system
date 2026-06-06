@extends('admin.layouts.master')

@section('page-title')
    تسوية بنكية
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
                    <h5 class="users-page-title">تسوية بنكية — {{ $bankReconciliation->bankAccount->name ?? '' }}</h5>
                    <a href="{{ route('admin.bank-reconciliations.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title"><i class="fas fa-university"></i> ملخص التسوية</h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">تاريخ الكشف</span>
                                        <div class="users-detail-item__value">{{ $bankReconciliation->statement_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رصيد الكشف</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($bankReconciliation->statement_balance, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">رصيد الدفاتر</span>
                                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($bankReconciliation->book_balance, 2) }}</span></div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الفرق</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-amount {{ abs($bankReconciliation->difference) < 0.01 ? 'users-qty--in' : 'users-qty--out' }}">
                                                {{ number_format($bankReconciliation->difference, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            @if ($bankReconciliation->status === \App\Models\BankReconciliation::STATUS_RECONCILED)
                                                <span class="users-badge users-badge--active">مُقفلة</span>
                                            @else
                                                <span class="users-badge users-badge--role">مسودة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($bankReconciliation->status === \App\Models\BankReconciliation::STATUS_DRAFT)
                    <div class="users-form-card" style="margin-top: 1.25rem;">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-plus"></i> إضافة بند</h6>
                        </div>
                        <form action="{{ route('admin.bank-reconciliations.items.store', $bankReconciliation) }}" method="POST" class="users-form-card__body">
                            @csrf
                            <div class="users-form-grid">
                                <div class="users-form-group">
                                    <label class="users-form-label">التاريخ</label>
                                    <input type="date" name="transaction_date" class="users-form-input" value="{{ date('Y-m-d') }}" required>
                                </div>
                                <div class="users-form-group">
                                    <label class="users-form-label">المبلغ</label>
                                    <input type="number" name="amount" step="0.01" class="users-form-input" required>
                                </div>
                                <div class="users-form-group users-form-group--full">
                                    <label class="users-form-label">الوصف</label>
                                    <input type="text" name="description" class="users-form-input">
                                </div>
                            </div>
                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit"><i class="fas fa-plus"></i> إضافة</button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="users-table-card" style="margin-top: 1.25rem;">
                    <div class="users-form-card__header" style="padding: 0.75rem 1.25rem; border-bottom: 1px solid var(--users-border);">
                        <h6 class="users-form-card__title" style="margin: 0;"><i class="fas fa-list"></i> بنود التسوية</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الوصف</th>
                                    <th>المبلغ</th>
                                    <th>مُسوّى</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bankReconciliation->items as $item)
                                    <tr>
                                        <td>{{ $item->transaction_date->format('Y-m-d') }}</td>
                                        <td>{{ $item->description ?? '—' }}</td>
                                        <td><span class="users-amount">{{ number_format($item->amount, 2) }}</span></td>
                                        <td>
                                            @if ($bankReconciliation->status === \App\Models\BankReconciliation::STATUS_DRAFT)
                                                <form action="{{ route('admin.bank-reconciliations.items.toggle', [$bankReconciliation, $item]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="users-badge {{ $item->is_cleared ? 'users-badge--active' : 'users-badge--inactive' }}" style="border: none; cursor: pointer;">
                                                        {{ $item->is_cleared ? 'نعم' : 'لا' }}
                                                    </button>
                                                </form>
                                            @else
                                                {{ $item->is_cleared ? 'نعم' : 'لا' }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="users-empty">لا توجد بنود</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($bankReconciliation->status === \App\Models\BankReconciliation::STATUS_DRAFT)
                    <div class="users-form-actions" style="margin-top: 1.25rem;">
                        <form action="{{ route('admin.bank-reconciliations.finalize', $bankReconciliation) }}" method="POST"
                            onsubmit="return confirm('هل تريد إقفال هذه التسوية؟');">
                            @csrf
                            <button type="submit" class="users-btn-submit"><i class="fas fa-lock"></i> إقفال التسوية</button>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
