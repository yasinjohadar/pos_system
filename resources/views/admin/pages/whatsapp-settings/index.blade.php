@extends('admin.layouts.master')

@section('page-title')
    إعدادات WhatsApp
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
                    <h5 class="users-page-title">إعدادات WhatsApp</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.whatsapp-messages.index') }}" class="users-btn-secondary">
                            <i class="fas fa-comments"></i>
                            الرسائل
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-whatsapp',
                        'title' => 'تكامل WhatsApp',
                        'text' => 'اربط النظام بـ Meta Cloud API أو مزود مخصص أو WhatsApp Web.',
                        'tips' => [
                            'اختبر الاتصال بعد حفظ الإعدادات',
                            'Webhook URL مطلوب في Meta Console',
                            'Access Token يُترك فارغاً للاحتفاظ بالقيمة الحالية',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fab fa-whatsapp"></i>
                                إعدادات التكامل
                            </h6>
                        </div>
                        <form action="{{ route('admin.whatsapp-settings.update') }}" method="POST"
                            id="whatsapp-settings-form" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.whatsapp-settings.partials.form-fields', [
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

            </div>
        </div>
    </div>

    @include('admin.pages.whatsapp-settings.partials.test-modal')
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script src="{{ asset('assets/js/whatsapp-settings-form.js') }}"></script>
    <script>
        AdminPremium.initFormToggles();
        WhatsAppSettingsForm.init({
            testUrl: @json(route('admin.whatsapp-settings.test-connection')),
        });
    </script>
@stop
