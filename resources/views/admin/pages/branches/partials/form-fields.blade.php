@php
    $nameValue = old('name', $branch?->name);
    $codeValue = old('code', $branch?->code);
    $emailValue = old('email', $branch?->email);
    $addressValue = old('address', $branch?->address);
    $isActiveChecked = (bool) old('is_active', $branch?->is_active ?? true);
@endphp

<div class="users-form-grid">
    <div class="users-form-group users-form-group--full">
        <label for="name" class="users-form-label">
            <i class="fas fa-building"></i>
            اسم الفرع
            <span class="users-form-required">*</span>
        </label>
        <input type="text"
            class="users-form-input @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ $nameValue }}"
            placeholder="مثال: فرع الرياض الرئيسي"
            required>
        @error('name')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="code" class="users-form-label">
            <i class="fas fa-barcode"></i>
            كود الفرع
        </label>
        <input type="text"
            class="users-form-input @error('code') is-invalid @enderror"
            id="code"
            name="code"
            value="{{ $codeValue }}"
            placeholder="مثال: BR01">
        <span class="users-form-hint">اختياري — يُستخدم للتعريف السريع</span>
        @error('code')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    @include('admin.components.premium.phone-input', [
        'name' => 'phone',
        'value' => old('phone', $branch?->phone),
        'label' => 'الهاتف',
    ])

    <div class="users-form-group">
        <label for="email" class="users-form-label">
            <i class="fas fa-envelope"></i>
            البريد الإلكتروني
        </label>
        <input type="email"
            class="users-form-input @error('email') is-invalid @enderror"
            id="email"
            name="email"
            value="{{ $emailValue }}"
            placeholder="branch@example.com"
            dir="ltr">
        @error('email')
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
            placeholder="عنوان الفرع أو موقعه">{{ $addressValue }}</textarea>
        @error('address')
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
            <span class="users-form-switch__title">فرع نشط</span>
            <span class="users-form-switch__desc">الفروع غير النشطة لا تظهر في قوائم الاختيار والعمليات</span>
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
