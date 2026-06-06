@extends('admin.layouts.master')

@section('page-title')
    إنشاء مستخدم جديد
@stop

@section('css')
    @include('admin.components.premium.styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>@include('admin.pages.users.partials.form-styles')</style>
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">إنشاء مستخدم جديد</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('users.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="users-form-layout">
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-user-plus',
                        'title' => 'مستخدم جديد',
                        'text' => 'أنشئ حساباً للوصول إلى لوحة التحكم وعيّن الأدوار المناسبة.',
                        'tips' => ['البريد الإلكتروني فريد', 'كلمة مرور قوية', 'يمكن تعيين أكثر من دور'],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-user"></i> بيانات المستخدم</h6>
                        </div>
                        @include('admin.pages.users.partials.form-fields', [
                            'action' => route('users.store'),
                            'submitLabel' => 'حفظ بيانات المستخدم',
                        ])
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include('admin.pages.users.partials.form-script')
@stop
