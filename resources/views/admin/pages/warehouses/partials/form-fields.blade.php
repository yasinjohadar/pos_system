@php
    $branchValue = old('branch_id', $selectedBranchId ?? $warehouse?->branch_id);
    $nameValue = old('name', $warehouse?->name);
    $codeValue = old('code', $warehouse?->code);
    $addressValue = old('address', $warehouse?->address);
    $isDefaultChecked = (bool) old('is_default', $warehouse?->is_default ?? false);
    $isActiveChecked = (bool) old('is_active', $warehouse?->is_active ?? true);
@endphp

<div class="users-form-grid">
    <div class="users-form-group users-form-group--full">
        <label for="branch_id" class="users-form-label">
            <i class="fas fa-building"></i>
            الفرع
            <span class="users-form-required">*</span>
        </label>
        <select class="users-form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
            <option value="">اختر الفرع</option>
            @foreach ($branches as $b)
                <option value="{{ $b->id }}" {{ (string) $branchValue === (string) $b->id ? 'selected' : '' }}>
                    {{ $b->name }}
                </option>
            @endforeach
        </select>
        @error('branch_id')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="name" class="users-form-label">
            <i class="fas fa-warehouse"></i>
            اسم المخزن
            <span class="users-form-required">*</span>
        </label>
        <input type="text"
            class="users-form-input @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ $nameValue }}"
            placeholder="مثال: المخزن الرئيسي"
            required>
        @error('name')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="code" class="users-form-label">
            <i class="fas fa-barcode"></i>
            كود المخزن
        </label>
        <input type="text"
            class="users-form-input @error('code') is-invalid @enderror"
            id="code"
            name="code"
            value="{{ $codeValue }}"
            placeholder="مثال: WH01">
        <span class="users-form-hint">اختياري — يُستخدم للتعريف السريع داخل النظام</span>
        @error('code')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group users-form-group--full">
        <label for="address" class="users-form-label">
            <i class="fas fa-map-marker-alt"></i>
            العنوان
        </label>
        <textarea class="users-form-textarea @error('address') is-invalid @enderror"
            id="address"
            name="address"
            rows="3"
            placeholder="عنوان المخزن أو موقعه الجغرافي">{{ $addressValue }}</textarea>
        @error('address')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="users-form-switches">
    <div class="users-form-switch">
        <div class="users-form-switch__icon users-form-switch__icon--default">
            <i class="fas fa-star"></i>
        </div>
        <div class="users-form-switch__info">
            <span class="users-form-switch__title">مخزن افتراضي للفرع</span>
            <span class="users-form-switch__desc">يُختار تلقائياً عند العمليات المرتبطة بهذا الفرع</span>
        </div>
        <label class="users-toggle users-toggle--compact">
            <input type="checkbox"
                class="users-toggle-input users-form-toggle-input"
                id="is_default"
                name="is_default"
                value="1"
                data-label-on="مفعّل"
                data-label-off="معطّل"
                {{ $isDefaultChecked ? 'checked' : '' }}>
            <span class="users-toggle-track">
                <span class="users-toggle-thumb"></span>
            </span>
            <span class="users-toggle-label">{{ $isDefaultChecked ? 'مفعّل' : 'معطّل' }}</span>
        </label>
    </div>

    <div class="users-form-switch">
        <div class="users-form-switch__icon users-form-switch__icon--active">
            <i class="fas fa-circle-check"></i>
        </div>
        <div class="users-form-switch__info">
            <span class="users-form-switch__title">مخزن نشط</span>
            <span class="users-form-switch__desc">المخازن غير النشطة لا تظهر في قوائم الاختيار</span>
        </div>
        <label class="users-toggle users-toggle--compact">
            <input type="checkbox"
                class="users-toggle-input users-form-toggle-input"
                id="is_active"
                name="is_active"
                value="1"
                data-label-on="نشط"
                data-label-off="غير نشط"
                {{ $isActiveChecked ? 'checked' : '' }}>
            <span class="users-toggle-track">
                <span class="users-toggle-thumb"></span>
            </span>
            <span class="users-toggle-label">{{ $isActiveChecked ? 'نشط' : 'غير نشط' }}</span>
        </label>
    </div>
</div>
