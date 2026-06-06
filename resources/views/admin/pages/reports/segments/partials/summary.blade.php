@php
    $totalCustomers = collect($rows)->sum('customer_count');
    $totalSales = collect($rows)->sum('total_sales');
    $totalInvoices = collect($rows)->sum('invoice_count');
    $segmentCount = count($rows);
@endphp

<div class="users-report-kpi-grid users-report-kpi-grid--3">
    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-layer-group"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">عدد الشرائح النشطة</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount" style="font-size: 1.25rem; font-weight: 700;">{{ $segmentCount }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-users"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">إجمالي العملاء</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount" style="font-size: 1.25rem; font-weight: 700;">{{ $totalCustomers }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">إجمالي المبيعات</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--in" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($totalSales, 2) }}</span>
                        <small class="users-muted-text d-block" style="margin-top: 0.35rem;">{{ $totalInvoices }} فاتورة</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
