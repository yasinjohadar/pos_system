@extends('admin.layouts.master')

@section('page-title')
    إضافة تصنيف
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">إضافة تصنيف</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.categories.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

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

                <div class="users-form-layout">
                    <aside class="users-form-aside">
                        <div class="users-form-aside__glow"></div>
                        <div class="users-form-aside__icon">
                            <i class="fas fa-folder-plus"></i>
                        </div>
                        <h6 class="users-form-aside__title">إنشاء تصنيف جديد</h6>
                        <p class="users-form-aside__text">
                            نظّم منتجاتك في تصنيفات رئيسية وفرعية لتسهيل البحث والتصفية.
                        </p>
                        <ul class="users-form-aside__tips">
                            <li><i class="fas fa-check"></i> التصنيفات الفرعية ترتبط بتصنيف أب</li>
                            <li><i class="fas fa-check"></i> الترتيب يحدد ظهور التصنيف في القوائم</li>
                            <li><i class="fas fa-check"></i> الصورة اختيارية وتحسّن العرض البصري</li>
                        </ul>
                    </aside>

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-tags"></i>
                                بيانات التصنيف
                            </h6>
                        </div>
                        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.categories.partials.form-fields', [
                                'parentCategories' => $parentCategories,
                                'category' => null,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    حفظ التصنيف
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="users-btn-secondary">
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
    <script>AdminPremium.initFormToggles();</script>
@stop
