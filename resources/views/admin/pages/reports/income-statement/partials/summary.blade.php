<div class="users-detail-grid" style="grid-template-columns: 1fr;">
    <div class="users-detail-card">
        <div class="users-detail-card__header">
            <h6 class="users-detail-card__title"><i class="fas fa-chart-line"></i> ملخص قائمة الدخل</h6>
        </div>
        <div class="users-detail-card__body">
            <div class="users-detail-list">
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-arrow-up"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">الإيرادات (حسابات إيرادات)</span>
                        <div class="users-detail-item__value">
                            <span class="users-amount users-qty--in">{{ number_format($revenue, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="users-detail-item">
                    <div class="users-detail-item__icon"><i class="fas fa-arrow-down"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label">المصروفات (حسابات مصروفات)</span>
                        <div class="users-detail-item__value">
                            <span class="users-amount users-qty--out">- {{ number_format($expense, 2) }}</span>
                        </div>
                    </div>
                </div>
                <div class="users-detail-item" style="border-top: 1px solid var(--users-border); padding-top: 1rem; margin-top: 0.5rem;">
                    <div class="users-detail-item__icon"><i class="fas fa-coins"></i></div>
                    <div class="users-detail-item__content">
                        <span class="users-detail-item__label" style="font-weight: 600;">صافي الدخل</span>
                        <div class="users-detail-item__value">
                            <span class="users-amount {{ $netIncome >= 0 ? 'users-qty--in' : 'users-qty--out' }}" style="font-size: 1.125rem; font-weight: 700;">
                                {{ number_format($netIncome, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
