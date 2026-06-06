@php
    $nameValue = old('name', $unit?->name);
    $symbolValue = old('symbol', $unit?->symbol);
    $baseUnitValue = old('base_unit_id', $unit?->base_unit_id);
    $conversionValue = old('conversion_factor', $unit?->conversion_factor ?? 1);
    $isActiveChecked = (bool) old('is_active', $unit?->is_active ?? true);
@endphp

<div class="users-form-grid">
    <div class="users-form-group users-form-group--full">
        <label for="name" class="users-form-label">
            <i class="fas fa-ruler-combined"></i>
            الاسم
            <span class="users-form-required">*</span>
        </label>
        <input type="text"
            class="users-form-input @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ $nameValue }}"
            placeholder="مثال: قطعة، كيلوغرام"
            required>
        @error('name')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="symbol" class="users-form-label">
            <i class="fas fa-font"></i>
            الرمز
        </label>
        <input type="text"
            class="users-form-input @error('symbol') is-invalid @enderror"
            id="symbol"
            name="symbol"
            value="{{ $symbolValue }}"
            placeholder="مثال: pcs, kg"
            dir="ltr">
        @error('symbol')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="conversion_factor" class="users-form-label">
            <i class="fas fa-calculator"></i>
            معامل التحويل
        </label>
        <input type="number"
            step="0.0001"
            class="users-form-input @error('conversion_factor') is-invalid @enderror"
            id="conversion_factor"
            name="conversion_factor"
            value="{{ $conversionValue }}"
            min="0.0001">
        <span class="users-form-hint">مثال: 1 صندوق = 12 قطعة ← 12</span>
        @error('conversion_factor')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group users-form-group--full">
        <label for="base_unit_id" class="users-form-label">
            <i class="fas fa-layer-group"></i>
            الوحدة الأساسية
        </label>
        <select class="users-form-select @error('base_unit_id') is-invalid @enderror" id="base_unit_id" name="base_unit_id">
            <option value="">— وحدة أساسية —</option>
            @foreach ($baseUnits as $u)
                <option value="{{ $u->id }}" {{ (string) $baseUnitValue === (string) $u->id ? 'selected' : '' }}>
                    {{ $u->name }}{{ $u->symbol ? ' (' . $u->symbol . ')' : '' }}
                </option>
            @endforeach
        </select>
        <span class="users-form-hint">اتركه فارغاً إذا كانت وحدة أساسية مستقلة</span>
        @error('base_unit_id')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="users-form-switches">
    <div class="users-form-switch">
        <div class="users-form-switch__icon users-form-switch__icon--active">
            <i class="fas fa-circle-check"></i>
        </div>
        <div class="users-form-switch__info">
            <span class="users-form-switch__title">وحدة نشطة</span>
            <span class="users-form-switch__desc">الوحدات غير النشطة لا تظهر في قوائم الاختيار</span>
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
