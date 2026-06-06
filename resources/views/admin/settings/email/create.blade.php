@extends('admin.layouts.master')

@section('page-title')
    إضافة إعدادات بريد إلكتروني
@stop

@section('css')
    @include('admin.components.premium.styles')
    <style>@include('admin.pages.users.partials.form-styles')</style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

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
                    <h5 class="users-page-title">إضافة إعدادات بريد إلكتروني</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.settings.email.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-envelope',
                        'title' => 'إعدادات SMTP جديدة',
                        'text' => 'كوّن خادم البريد لإرسال الإشعارات والرسائل من النظام.',
                        'tips' => [
                            'Gmail يتطلب App Password',
                            'اختبر الاتصال قبل الحفظ',
                            'يمكنك حفظ أكثر من إعداد وتفعيل واحد فقط',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-mail-bulk"></i>
                                تكوين SMTP
                            </h6>
                        </div>
                        <form action="{{ route('admin.settings.email.store') }}" method="POST" class="users-form-card__body">
                            @csrf

                            @include('admin.settings.email.partials.form-fields', [
                                'providers' => $providers,
                                'isEdit' => false,
                            ])

                            <div class="users-form-actions">
                                <button type="button" class="users-btn-secondary" onclick="testEmailConnection(event)">
                                    <i class="fas fa-vial"></i>
                                    اختبار الاتصال
                                </button>
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    حفظ الإعدادات
                                </button>
                                <a href="{{ route('admin.settings.email.index') }}" class="users-btn-secondary">
                                    إلغاء
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    @include('admin.settings.email.partials.form-scripts', ['isEdit' => false])
@stop
