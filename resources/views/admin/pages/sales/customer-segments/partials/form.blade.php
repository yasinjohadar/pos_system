@php
    $segment = $customerSegment ?? null;
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-users-between-lines',
            'title' => $segment ? 'تعديل شريحة عملاء' : 'شريحة عملاء جديدة',
            'text' => 'صنّف عملاءك إلى شرائح (VIP، جملة، تجزئة) لتطبيق أسعار وعروض مخصصة.',
            'tips' => ['اختر لوناً مميزاً للشريحة', 'الاسم الإنجليزي اختياري للتقارير', 'يمكن ربط العملاء بالشريحة لاحقاً'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-layer-group"></i> بيانات الشريحة</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> الاسم (عربي) <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror" value="{{ old('name', $segment->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="users-form-group">
                        <label for="name_en" class="users-form-label"><i class="fas fa-language"></i> الاسم (إنجليزي)</label>
                        <input type="text" name="name_en" id="name_en" class="users-form-input" value="{{ old('name_en', $segment->name_en ?? '') }}" dir="ltr">
                    </div>
                    <div class="users-form-group users-form-group--full">
                        <label for="description" class="users-form-label"><i class="fas fa-align-right"></i> الوصف</label>
                        <textarea name="description" id="description" class="users-form-textarea" rows="3">{{ old('description', $segment->description ?? '') }}</textarea>
                    </div>
                    <div class="users-form-group">
                        <label for="color" class="users-form-label"><i class="fas fa-palette"></i> اللون</label>
                        <div class="users-color-field">
                            <input type="color" name="color" id="color" class="users-form-color" value="{{ old('color', $segment->color ?? '#6366f1') }}">
                            <span class="users-color-preview" id="color-preview" style="background: {{ old('color', $segment->color ?? '#6366f1') }};"></span>
                            <span class="users-color-value" id="color-value">{{ old('color', $segment->color ?? '#6366f1') }}</span>
                        </div>
                    </div>
                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input" {{ old('is_active', $segment->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">الشريحة نشطة</span>
                        </label>
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.customer-segments.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
