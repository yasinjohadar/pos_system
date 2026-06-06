@extends('admin.layouts.master')

@section('page-title')
    تعديل الوحدة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل الوحدة: {{ $unit->name }}</h5>
                    <div class="users-header-actions">
                        <a href="{{ route('admin.units.index') }}" class="users-btn-secondary">
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
                        'icon' => 'fa-pen-to-square',
                        'title' => 'تعديل بيانات الوحدة',
                        'text' => 'حدّث معلومات الوحدة مع الحفاظ على صحة معامل التحويل والارتباط بالوحدة الأساسية.',
                        'tips' => [
                            'تغيير معامل التحويل يؤثر على الحسابات المرتبطة',
                            'لا يمكن جعل الوحدة أباً لنفسها',
                            'إيقاف التفعيل يخفيها من القوائم',
                        ],
                    ])

                    <div class="users-form-card">
                        <div class="users-form-card__header">
                            <h6 class="users-form-card__title">
                                <i class="fas fa-ruler-combined"></i>
                                بيانات الوحدة
                            </h6>
                        </div>
                        <form action="{{ route('admin.units.update', $unit) }}" method="POST" class="users-form-card__body">
                            @csrf
                            @method('PUT')

                            @include('admin.pages.units.partials.form-fields', [
                                'baseUnits' => $baseUnits,
                                'unit' => $unit,
                            ])

                            <div class="users-form-actions">
                                <button type="submit" class="users-btn-submit">
                                    <i class="fas fa-save"></i>
                                    حفظ التعديلات
                                </button>
                                <a href="{{ route('admin.units.index') }}" class="users-btn-secondary">إلغاء</a>
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
