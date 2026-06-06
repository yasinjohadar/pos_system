@extends('admin.layouts.master')

@section('page-title')
    إنشاء نسخة احتياطية
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
                    <h5 class="users-page-title">إنشاء نسخة احتياطية</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.backups.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-shield-alt',
                        'title' => 'نسخة احتياطية جديدة',
                        'text' => 'أنشئ نسخة من قاعدة البيانات أو الملفات واحفظها في مكان التخزين المحدد.',
                        'tips' => [
                            'اختر مكان تخزين نشطاً',
                            'ZIP مناسب لمعظم الحالات',
                            'يُحذف تلقائياً بعد انتهاء مدة الاحتفاظ',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-plus-circle"></i>
                                إعدادات النسخة
                            </h6>
                        </div>
                        <form action="{{ route('admin.backups.store') }}" method="POST" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.backups.partials.form-fields', [
                                'backupTypes' => $backupTypes,
                                'compressionTypes' => $compressionTypes,
                                'storageConfigs' => $storageConfigs,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit" {{ $storageConfigs->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i>
                                    إنشاء النسخة
                                </button>
                                <a href="{{ route('admin.backups.index') }}" class="users-btn-secondary">إلغاء</a>
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
@stop
