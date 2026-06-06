@extends('admin.layouts.master')

@section('page-title')
    إضافة منتج
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">إضافة منتج</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.products.index') }}" class="users-btn-secondary">
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
                    @include('admin.components.premium.form-aside', [
                        'icon' => 'fa-box-open',
                        'title' => 'إنشاء منتج جديد',
                        'text' => 'أضف منتجاً مع تصنيفه ووحدته وأسعاره لتجهيز المخزون ونقطة البيع.',
                        'tips' => [
                            'الباركود يُستخدم للبحث السريع في نقطة البيع',
                            'السعر الأساسي يُطبَّق على جميع الفروع افتراضياً',
                            'حد تنبيه المخزون يُفعّل التنبيهات عند النقص',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-box"></i>
                                بيانات المنتج
                            </h6>
                        </div>
                        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="users-form-card__body">
                            @csrf

                            @include('admin.pages.products.partials.form-fields', [
                                'categories' => $categories,
                                'units' => $units,
                                'taxes' => $taxes,
                                'product' => null,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    حفظ المنتج
                                </button>
                                <a href="{{ route('admin.products.index') }}" class="users-btn-secondary">إلغاء</a>
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
