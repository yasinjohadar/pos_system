<div class="users-detail-card">
    <div class="users-detail-card__header">
        <h6 class="users-detail-card__title">
            <i class="fas fa-qrcode"></i>
            حالة الاتصال
        </h6>
    </div>
    <div class="users-detail-card__body" id="connection-status-card">
        @if ($session && $session->isConnected())
            <div class="text-center py-2" id="connected-status">
                <div class="mb-3">
                    <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-success mb-3">متصل بنجاح</h5>
                <div class="users-detail-list">
                    <div class="users-detail-item">
                        <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                        <div class="users-detail-item__content">
                            <span class="users-detail-item__label">الاسم</span>
                            <div class="users-detail-item__value" id="session-name">{{ $session->name ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div class="users-detail-item">
                        <div class="users-detail-item__icon"><i class="fas fa-phone"></i></div>
                        <div class="users-detail-item__content">
                            <span class="users-detail-item__label">رقم الهاتف</span>
                            <div class="users-detail-item__value users-color-value" id="session-phone">{{ $session->phone_number ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div class="users-detail-item">
                        <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                        <div class="users-detail-item__content">
                            <span class="users-detail-item__label">تاريخ الاتصال</span>
                            <div class="users-detail-item__value" id="session-date">{{ $session->connected_at?->format('Y-m-d H:i:s') ?? 'غير محدد' }}</div>
                        </div>
                    </div>
                </div>
                <div class="users-form-actions" style="justify-content: center; margin-top: 1rem;">
                    <button type="button" class="users-btn-secondary" id="refresh-status-btn">
                        <i class="fas fa-sync-alt"></i>
                        تحديث الحالة
                    </button>
                    <button type="button" class="users-btn-secondary users-action-btn--delete" id="disconnect-btn"
                        data-session-id="{{ $session->session_id }}">
                        <i class="fas fa-unlink"></i>
                        قطع الاتصال
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-3">
                <div class="mb-3">
                    <i class="fas fa-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-warning mb-2">غير متصل</h5>
                <p class="users-muted-text mb-3">لم يتم ربط أي جهاز بعد</p>
                <a href="{{ route('admin.whatsapp-web.connect') }}" class="users-btn-submit">
                    <i class="fas fa-qrcode"></i>
                    ربط WhatsApp Web
                </a>
            </div>
        @endif
    </div>
</div>
