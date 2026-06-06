@php
    $paymentMethod = $paymentMethod ?? null;
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-credit-card',
            'title' => $paymentMethod ? 'تعديل طريقة دفع' : 'طريقة دفع جديدة',
            'text' => 'عرّف طرق الدفع المتاحة عند تسجيل دفعات الفواتير (نقدي، بطاقة، تحويل، إلخ).',
            'tips' => ['الكود يُستخدم برمجياً — لا تغيّره بعد الاستخدام', 'رتّب العرض حسب الأولوية', 'الطرق غير النشطة لا تظهر في القوائم'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-wallet"></i> بيانات طريقة الدفع</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> الاسم <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror"
                            value="{{ old('name', optional($paymentMethod)->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="code" class="users-form-label"><i class="fas fa-code"></i> الكود <span class="users-form-required">*</span></label>
                        <input type="text" name="code" id="code" class="users-form-input @error('code') is-invalid @enderror"
                            value="{{ old('code', optional($paymentMethod)->code) }}" placeholder="مثال: cash" dir="ltr" required>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="sort_order" class="users-form-label"><i class="fas fa-sort-numeric-down"></i> ترتيب العرض</label>
                        <input type="number" name="sort_order" id="sort_order" class="users-form-input @error('sort_order') is-invalid @enderror"
                            value="{{ old('sort_order', optional($paymentMethod)->sort_order ?? 0) }}" min="0">
                        @error('sort_order')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input"
                                {{ old('is_active', optional($paymentMethod)->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">نشط</span>
                        </label>
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.payment-methods.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
