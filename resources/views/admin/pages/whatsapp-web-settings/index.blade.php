@extends('admin.layouts.master')

@section('page-title')
    إعدادات WhatsApp Web
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>@include('admin.pages.users.partials.form-styles')</style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>يرجى تصحيح الأخطاء التالية:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-header">
                    <h5 class="users-page-title">إعدادات WhatsApp Web</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.whatsapp-settings.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            إعدادات WhatsApp العامة
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid" style="margin-bottom: 1.25rem;">
                    @include('admin.pages.whatsapp-web-settings.partials.connection-status', [
                        'session' => $session,
                    ])
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-qrcode',
                        'title' => 'WhatsApp Web',
                        'text' => 'اربط جهازك عبر QR Code من خلال خدمة Node.js المحلية.',
                        'tips' => [
                            'شغّل Node.js service قبل الاختبار',
                            'الفواصل الزمنية تحمي من الحظر',
                            'API Token اختياري إذا مُفعّل في .env',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-cog"></i>
                                إعدادات الخدمة
                            </h6>
                        </div>
                        <form action="{{ route('admin.whatsapp-web-settings.update') }}" method="POST"
                            id="whatsapp-web-settings-form" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.whatsapp-web-settings.partials.form-fields', [
                                'settings' => $settings,
                            ])

                            <div class="users-form-actions">
                                <button type="button" id="test-connection-btn" class="users-btn-secondary">
                                    <i class="fas fa-vial"></i>
                                    اختبار الاتصال
                                </button>
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    حفظ الإعدادات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="users-detail-grid" style="margin-top: 1.25rem;">
                    @include('admin.pages.whatsapp-web-settings.partials.setup-instructions')
                </div>

            </div>
        </div>
    </div>

    @include('admin.pages.whatsapp-web-settings.partials.test-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script src="{{ asset('assets/js/whatsapp-web-settings-form.js') }}"></script>
    <script>
        AdminPremium.initFormToggles();
        WhatsAppWebSettingsForm.init({
            testUrl: @json(route('admin.whatsapp-web-settings.test-connection')),
            statusUrlTemplate: @json(url('admin/whatsapp-web/status/__SESSION__')),
            disconnectUrlTemplate: @json(url('admin/whatsapp-web/disconnect/__SESSION__')),
            sessionId: @json($session && $session->isConnected() ? $session->session_id : null),
            autoRefresh: @json($session && $session->isConnected()),
        });
    </script>
@stop
