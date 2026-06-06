@extends('admin.layouts.master')

@section('page-title')
    تعديل ضريبة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                <div class="users-header">
                    <h5 class="users-page-title">تعديل ضريبة: {{ $tax->name }}</h5>
                    <a href="{{ route('admin.taxes.index') }}" class="users-btn-secondary">
                        <i class="fas fa-arrow-right"></i> رجوع
                    </a>
                </div>

                @include('admin.components.premium.flash')

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.taxes.update', $tax) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="users-form-layout">
                        @include('admin.components.premium.form-aside', [
                            'icon' => 'fa-percent',
                            'title' => 'تعديل الضريبة',
                            'text' => 'تحديث بيانات الضريبة المستخدمة في النظام.',
                            'tips' => ['تغيير النسبة لا يؤثر على الفواتير السابقة'],
                        ])
                        <div class="users-form-card">
                            <div class="users-form-card__header">
                                <h6 class="users-form-card__title"><i class="fas fa-percent"></i> بيانات الضريبة</h6>
                            </div>
                            <div class="users-form-card__body">
                                <div class="users-form-grid">
                                    <div class="users-form-group">
                                        <label for="name" class="users-form-label">الاسم <span class="users-form-required">*</span></label>
                                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror" value="{{ old('name', $tax->name) }}" required>
                                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label for="type" class="users-form-label">النوع <span class="users-form-required">*</span></label>
                                        <select name="type" id="type" class="users-form-select @error('type') is-invalid @enderror" required>
                                            <option value="percent" {{ old('type', $tax->type) === 'percent' ? 'selected' : '' }}>نسبة مئوية</option>
                                            <option value="fixed" {{ old('type', $tax->type) === 'fixed' ? 'selected' : '' }}>مبلغ ثابت</option>
                                        </select>
                                        @error('type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group">
                                        <label for="rate" class="users-form-label">النسبة / القيمة <span class="users-form-required">*</span></label>
                                        <input type="number" name="rate" id="rate" step="0.0001" min="0" class="users-form-input @error('rate') is-invalid @enderror" value="{{ old('rate', $tax->rate) }}" required>
                                        @error('rate')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="users-form-group users-form-group--full">
                                        <label class="users-form-toggle">
                                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input" {{ old('is_active', $tax->is_active) ? 'checked' : '' }}>
                                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                                            <span class="users-form-toggle-label">نشط</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="users-form-actions">
                                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> حفظ التعديلات</button>
                                    <a href="{{ route('admin.taxes.index') }}" class="users-btn-secondary">إلغاء</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
    <script>AdminPremium.initFormToggles();</script>
@stop
