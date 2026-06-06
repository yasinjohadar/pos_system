@extends('admin.layouts.master')

@section('page-title')
    تعديل إعدادات التخزين
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
                    <h5 class="users-page-title">تعديل: {{ $config->name }}</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.backup-storage.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-hdd',
                        'title' => 'تحديث التخزين',
                        'text' => 'عدّل إعدادات الاتصال أو الأولوية. اترك حقول كلمات المرور فارغة للاحتفاظ بالقيم الحالية.',
                        'tips' => [
                            'اختبر الاتصال بعد التعديل',
                            'تغيير النوع قد يتطلب إعدادات جديدة',
                            'حدّد الحد الأقصى للنسخ لتوفير المساحة',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-database"></i> بيانات التخزين</h6>
                        </div>
                        <form action="{{ route('admin.backup-storage.update', $config->id) }}" method="POST" id="storage-form"
                            class="users-form-card__body"
                            data-test-url="{{ route('admin.backup-storage.test-connection') }}"
                            data-current-config='@json($config->getDecryptedConfig() ?? [])'>
                            @csrf
                            @method('PUT')

                            @include('admin.pages.backup-storage.partials.form-fields', [
                                'drivers' => $drivers,
                                'config' => $config,
                            ])

                            <div class="users-form-actions">
                                <button type="button" id="test-connection-btn" class="users-btn-secondary">
                                    <i class="fas fa-vial"></i>
                                    اختبار الاتصال
                                </button>
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    تحديث
                                </button>
                                <a href="{{ route('admin.backup-storage.index') }}" class="users-btn-secondary">إلغاء</a>
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
    <script src="{{ asset('assets/js/backup-storage-form.js') }}"></script>
    <script>AdminPremium.initFormToggles(); BackupStorageForm.init();</script>
@stop
