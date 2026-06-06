@extends('admin.layouts.master')

@section('page-title')
    تعديل المستخدم
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
                    <h5 class="users-page-title">تعديل المستخدم: {{ $user->name }}</h5>
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
                        'icon' => 'fa-user-edit',
                        'title' => 'تعديل المستخدم',
                        'text' => 'حدّث بيانات الحساب والأدوار. لتغيير كلمة المرور استخدم خيار «تغيير كلمة المرور» من قائمة المستخدمين.',
                        'tips' => ['تحديث الأدوار يؤثر فوراً', 'تعطيل الحساب يمنع الدخول'],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title"><i class="fas fa-user"></i> بيانات المستخدم</h6>
                        </div>
                        @include('admin.pages.users.partials.form-fields', [
                            'action' => route('users.update', $user->id),
                            'user' => $user,
                            'submitLabel' => 'حفظ التعديلات',
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
