@extends('admin.layouts.master')

@section('page-title')
    إضافة ربط قرص
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
                    <h5 class="users-page-title">إضافة ربط قرص</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.storage-disk-mappings.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-link',
                        'title' => 'ربط قرص جديد',
                        'text' => 'اربط اسم قرص منطقي (مثل images) بمكان تخزين فعلي في النظام.',
                        'tips' => [
                            'Disk Name يُستخدم في الكود',
                            'Fallback يُفعّل عند فشل التخزين الأساسي',
                            'حدّد أنواع الملفات لتقييد الاستخدام',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-hdd"></i> بيانات الربط</h6>
                        </div>
                        <form action="{{ route('admin.storage-disk-mappings.store') }}" method="POST" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.storage-disk-mappings.partials.form-fields', [
                                'storages' => $storages,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit" {{ $storages->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i>
                                    حفظ
                                </button>
                                <a href="{{ route('admin.storage-disk-mappings.index') }}" class="users-btn-secondary">إلغاء</a>
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
    <script>AdminPremium.initFormToggles();</script>
@stop
