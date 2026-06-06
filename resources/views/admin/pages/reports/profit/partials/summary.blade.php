@php
    $netSales = $summary['sales_total'] - $summary['sales_returns'];
    $netPurchases = $summary['purchases_total'] - $summary['purchase_returns'];
    $grossProfit = $summary['gross_profit'];
@endphp

<div class="users-report-kpi-grid users-report-kpi-grid--4">
    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">إجمالي المبيعات (بعد المرتجعات)</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--in" style="font-size: 1.125rem; font-weight: 700;">{{ number_format($netSales, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-truck"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">إجمالي المشتريات (بعد المرتجعات)</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--out" style="font-size: 1.125rem; font-weight: 700;">{{ number_format($netPurchases, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">إيرادات أخرى (سندات قبض)</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--in" style="font-size: 1.125rem; font-weight: 700;">{{ number_format($summary['vouchers_receipts'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">مصروفات أخرى (سندات صرف)</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--out" style="font-size: 1.125rem; font-weight: 700;">{{ number_format($summary['vouchers_payments'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="users-detail-card" style="margin-top: 1.25rem;">
    <div class="users-detail-card__body" style="padding: 1.5rem 1.25rem;">
        <div class="users-detail-item" style="margin: 0; align-items: center;">
            <div class="users-detail-item__icon" style="width: 3rem; height: 3rem; font-size: 1.25rem;"><i class="fas fa-chart-line"></i></div>
            <div class="users-detail-item__content">
                <span class="users-detail-item__label" style="font-size: 0.9375rem;">الربح الإجمالي التقديري</span>
                <div class="users-detail-item__value">
                    <span class="users-amount {{ $grossProfit >= 0 ? 'users-qty--in' : 'users-qty--out' }}" style="font-size: 1.5rem; font-weight: 700;">
                        {{ number_format($grossProfit, 2) }}
                    </span>
                    <small class="users-muted-text d-block" style="margin-top: 0.35rem;">
                        من {{ $from->format('Y-m-d') }} إلى {{ $to->format('Y-m-d') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="users-detail-card" style="margin-top: 1.25rem;">
    <div class="users-detail-card__header">
        <h6 class="users-detail-card__title"><i class="fas fa-calculator"></i> تفاصيل الحساب</h6>
    </div>
    <div class="users-detail-card__body">
        <div class="users-detail-list">
            <div class="users-detail-item">
                <div class="users-detail-item__icon"><i class="fas fa-receipt"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">مبيعات خام</span>
                    <div class="users-detail-item__value"><span class="users-amount">{{ number_format($summary['sales_total'], 2) }}</span></div>
                </div>
            </div>
            <div class="users-detail-item">
                <div class="users-detail-item__icon"><i class="fas fa-undo"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">مرتجعات المبيعات</span>
                    <div class="users-detail-item__value"><span class="users-amount users-qty--out">- {{ number_format($summary['sales_returns'], 2) }}</span></div>
                </div>
            </div>
            <div class="users-detail-item">
                <div class="users-detail-item__icon"><i class="fas fa-file-invoice"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">مشتريات خام</span>
                    <div class="users-detail-item__value"><span class="users-amount">{{ number_format($summary['purchases_total'], 2) }}</span></div>
                </div>
            </div>
            <div class="users-detail-item">
                <div class="users-detail-item__icon"><i class="fas fa-undo-alt"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">مرتجعات المشتريات</span>
                    <div class="users-detail-item__value"><span class="users-amount users-qty--in">- {{ number_format($summary['purchase_returns'], 2) }}</span></div>
                </div>
            </div>
        </div>
        <p class="users-muted-text" style="margin: 1rem 0 0; font-size: 0.8125rem;">
            الصيغة: (صافي المبيعات − صافي المشتريات) + سندات القبض − سندات الصرف
        </p>
    </div>
</div>
