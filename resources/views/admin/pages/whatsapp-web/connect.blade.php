@extends('admin.layouts.master')

@section('page-title')
    ربط WhatsApp Web
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>@include('admin.pages.users.partials.form-styles')</style>
    <style>
        .whatsapp-qr-card {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 280px;
            padding: 1.25rem;
            border-radius: 12px;
            background: #fff;
            border: 1px solid var(--users-border, #e5e7eb);
        }

        .whatsapp-qr-card img,
        .whatsapp-qr-card svg {
            max-width: 280px;
            height: auto;
        }
    </style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">ربط WhatsApp Web</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.whatsapp-web-settings.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            العودة للإعدادات
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    <div class="users-form-card" style="max-width: 720px; margin: 0 auto; width: 100%;">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-qrcode"></i>
                                حالة الاتصال
                            </h6>
                        </div>
                        <div class="users-form-card__body">
                            @if ($session && $session->isConnected())
                                <div class="text-center py-3">
                                    <div class="mb-3">
                                        <i class="fas fa-check-circle text-success" style="font-size: 3.5rem;"></i>
                                    </div>
                                    <h5 class="text-success mb-3">متصل بنجاح</h5>
                                    <div class="users-detail-list mb-4">
                                        <div class="users-detail-item">
                                            <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                                            <div class="users-detail-item__content">
                                                <span class="users-detail-item__label">الاسم</span>
                                                <div class="users-detail-item__value">{{ $session->name ?? 'غير محدد' }}</div>
                                            </div>
                                        </div>
                                        <div class="users-detail-item">
                                            <div class="users-detail-item__icon"><i class="fas fa-phone"></i></div>
                                            <div class="users-detail-item__content">
                                                <span class="users-detail-item__label">رقم الهاتف</span>
                                                <div class="users-detail-item__value users-color-value">{{ $session->phone_number ?? 'غير محدد' }}</div>
                                            </div>
                                        </div>
                                        <div class="users-detail-item">
                                            <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                            <div class="users-detail-item__content">
                                                <span class="users-detail-item__label">تاريخ الاتصال</span>
                                                <div class="users-detail-item__value">{{ $session->connected_at?->format('Y-m-d H:i:s') ?? 'غير محدد' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="users-btn-secondary users-action-btn--delete"
                                        id="disconnect-session-btn" data-session-id="{{ $session->session_id }}">
                                        <i class="fas fa-unlink"></i>
                                        قطع الاتصال
                                    </button>
                                </div>
                            @else
                                <div class="text-center py-2">
                                    <div id="qr-container" class="mb-4" hidden>
                                        <h6 class="mb-3">امسح QR Code باستخدام WhatsApp</h6>
                                        <div class="whatsapp-qr-card mb-3">
                                            <div id="qr-code-display"></div>
                                        </div>
                                        <p class="users-muted-text small mb-0">
                                            <i class="fas fa-info-circle"></i>
                                            افتح WhatsApp → الإعدادات → الأجهزة المرتبطة → ربط جهاز
                                        </p>
                                    </div>

                                    <div id="loading-container" class="text-center py-4" hidden>
                                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                                        <p class="users-muted-text mb-0">جاري إعداد الاتصال...</p>
                                    </div>

                                    <div id="error-container" class="email-form-alert email-form-alert--warning mb-4" hidden>
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>
                                            <strong id="error-message"></strong>
                                            <br>
                                            <small class="users-muted-text">
                                                تحقق من تشغيل Node.js على
                                                <span class="users-color-value">{{ $nodejsUrl ?? 'http://localhost:3000' }}</span>
                                                — راجع <code>whatsapp-web-service-README.md</code>
                                            </small>
                                        </span>
                                    </div>

                                    <div id="action-buttons" class="mb-4">
                                        <button type="button" class="users-btn-submit" id="start-connection-btn">
                                            <i class="fas fa-qrcode"></i>
                                            بدء الربط
                                        </button>
                                    </div>

                                    <div class="email-form-alert">
                                        <i class="fas fa-info-circle"></i>
                                        <span>
                                            <strong>مهم:</strong> يجب إعداد Node.js service أولاً قبل استخدام هذه الميزة.
                                            راجع ملف <code>whatsapp-web-service-README.md</code> في المجلد الرئيسي للمشروع.
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    @if (!($session && $session->isConnected()))
        <script src="{{ asset('assets/js/whatsapp-web-connect.js') }}"></script>
        <script>
            WhatsAppWebConnect.init({
                startUrl: @json(route('admin.whatsapp-web.start-connection')),
                statusUrlTemplate: @json(url('admin/whatsapp-web/status/__SESSION__')),
                disconnectUrlTemplate: @json(url('admin/whatsapp-web/disconnect/__SESSION__')),
                nodejsUrl: @json($nodejsUrl ?? 'http://localhost:3000'),
            });
        </script>
    @else
        <script src="{{ asset('assets/js/whatsapp-web-connect.js') }}"></script>
        <script>
            WhatsAppWebConnect.init({
                disconnectUrlTemplate: @json(url('admin/whatsapp-web/disconnect/__SESSION__')),
            });
        </script>
    @endif
@stop
