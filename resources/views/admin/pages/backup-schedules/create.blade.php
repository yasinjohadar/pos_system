@extends('admin.layouts.master')

@section('page-title')
    إنشاء جدولة جديدة
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
                    <h5 class="users-page-title">إنشاء جدولة جديدة</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.backup-schedules.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-clock',
                        'title' => 'جدولة تلقائية',
                        'text' => 'حدّد مواعيد النسخ الاحتياطي التلقائي حسب نوع المحتوى والتكرار المناسب.',
                        'tips' => [
                            'النسخ اليومي في وقت منخفض الحركة',
                            'اختَر مكان تخزين نشطاً',
                            'حدّد مدة الاحتفاظ لتوفير المساحة',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-calendar-plus"></i> بيانات الجدولة</h6>
                        </div>
                        <form action="{{ route('admin.backup-schedules.store') }}" method="POST" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.backup-schedules.partials.form-fields', [
                                'backupTypes' => $backupTypes,
                                'frequencies' => $frequencies,
                                'storageConfigs' => $storageConfigs,
                                'compressionTypes' => $compressionTypes,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit" {{ $storageConfigs->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i>
                                    حفظ
                                </button>
                                <a href="{{ route('admin.backup-schedules.index') }}" class="users-btn-secondary">إلغاء</a>
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
    <script src="{{ asset('assets/js/backup-schedule-form.js') }}"></script>
    <script>BackupScheduleForm.init();</script>
@stop
