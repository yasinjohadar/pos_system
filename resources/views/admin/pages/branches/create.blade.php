@extends('admin.layouts.master')

@section('page-title')
    إضافة فرع
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">إضافة فرع</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.branches.index') }}" class="users-btn-secondary">
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
                            <i class="fas fa-building"></i>
                        </div>
                        <h6 class="users-form-aside__title">إنشاء فرع جديد</h6>
                        <p class="users-form-aside__text">
                            أضف فرعاً جديداً لإدارة المخازن والمبيعات والعمليات المرتبطة به بشكل منفصل.
                        </p>
                        <ul class="users-form-aside__tips">
                            <li><i class="fas fa-check"></i> اختر اسماً واضحاً يميّز الفرع</li>
                            <li><i class="fas fa-check"></i> أضف الهاتف والبريد للتواصل السريع</li>
                            <li><i class="fas fa-check"></i> الكود اختياري ويسهّل البحث والتصفية</li>
                        </ul>
                    </aside>

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-building"></i>
                                بيانات الفرع
                            </h6>
                        </div>
                        <form action="{{ route('admin.branches.store') }}" method="POST" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.branches.partials.form-fields', [
                                'branch' => null,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    حفظ الفرع
                                </button>
                                <a href="{{ route('admin.branches.index') }}" class="users-btn-secondary">
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
