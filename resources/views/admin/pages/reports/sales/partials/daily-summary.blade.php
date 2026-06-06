<div class="users-report-kpi-grid">
    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-file-invoice"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">عدد الفواتير المؤكدة</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount" style="font-size: 1.25rem; font-weight: 700;">{{ $summary['invoices_count'] }}</span>
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
                        <span class="users-amount users-qty--in" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($summary['total_sales'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-detail-card">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-coins"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">صافي المبيعات</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($summary['net_sales'], 2) }}</span>
                        <small class="users-muted-text d-block" style="margin-top: 0.35rem;">
                            مرتجعات: {{ number_format($summary['total_returns'], 2) }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($summary['invoices_count'] > 0 || $summary['total_returns'] > 0)
    <div class="users-detail-card" style="margin-top: 1.25rem;">
        <div class="users-detail-card__header">
            <h6 class="users-detail-card__title"><i class="fas fa-chart-pie"></i> تفاصيل إضافية — {{ $date->format('Y-m-d') }}</h6>
        </div>
        <div class="users-detail-card__body">
            <div class="users-detail-list">
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-percent"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">إجمالي الضريبة</span>
                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($summary['tax_amount'], 2) }}</span></div>
                    </div>
                </div>
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-tag"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">إجمالي الخصم</span>
                        <div class="users-detail-item__value"><span class="users-amount">{{ number_format($summary['discount_amount'], 2) }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="users-table-card" style="margin-top: 1.25rem;">
        <div class="users-empty" style="padding: 2.5rem 1rem;">
            لا توجد مبيعات مؤكدة في هذا التاريخ — جرّب تاريخاً آخر أو شغّل <code>SalesSeeder</code>
        </div>
    </div>
@endif
