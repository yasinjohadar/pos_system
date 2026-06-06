@php
    $netTax = $salesTax - $purchaseTax;
@endphp

<div class="users-report-kpi-grid users-report-kpi-grid--3">
    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">ضريبة المخرجات (المبيعات)</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--in" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($salesTax, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-truck-loading"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">ضريبة المدخلات (المشتريات)</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--out" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($purchaseTax, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-balance-scale"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">صافي الضريبة المستحقة</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount {{ $netTax >= 0 ? 'users-qty--out' : 'users-qty--in' }}" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($netTax, 2) }}</span>
                        <small class="users-muted-text d-block" style="margin-top: 0.35rem;">
                            مخرجات − مدخلات
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($salesTax == 0 && $purchaseTax == 0)
    <div class="users-table-card" style="margin-top: 1.25rem;">
        <div class="users-empty" style="padding: 2.5rem 1rem;">
            لا توجد ضرائب في الفترة المحددة — جرّب توسيع التاريخ أو شغّل <code>SalesSeeder</code> و<code>PurchaseSeeder</code>
        </div>
    </div>
@else
    <div class="users-detail-card" style="margin-top: 1.25rem;">
        <div class="users-detail-card__header">
            <h6 class="users-detail-card__title"><i class="fas fa-calendar-alt"></i> ملخص الفترة — {{ $from->format('Y-m-d') }} إلى {{ $to->format('Y-m-d') }}</h6>
        </div>
        <div class="users-detail-card__body">
            <div class="users-detail-list">
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-percent"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">ضريبة المبيعات المحصّلة</span>
                        <div class="users-detail-item__value"><span class="users-amount users-qty--in">{{ number_format($salesTax, 2) }}</span></div>
                    </div>
                </div>
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-receipt"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">ضريبة المشتريات القابلة للخصم</span>
                        <div class="users-detail-item__value"><span class="users-amount users-qty--out">{{ number_format($purchaseTax, 2) }}</span></div>
                    </div>
                </div>
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-calculator"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">الصافي للتوريد / الاسترداد</span>
                        <div class="users-detail-item__value">
                            <span class="users-amount {{ $netTax >= 0 ? 'users-qty--out' : 'users-qty--in' }}">{{ number_format(abs($netTax), 2) }}</span>
                            <small class="users-muted-text d-block" style="margin-top: 0.25rem;">{{ $netTax >= 0 ? 'مستحق للتوريد' : 'قابل للاسترداد' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
