@php
    $supplier = $supplier ?? null;
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-truck',
            'title' => $supplier ? 'تعديل مورد' : 'مورد جديد',
            'text' => 'سجّل بيانات المورد لربطها بفواتير الشراء والدفعات وكشف الحساب.',
            'tips' => ['الرصيد الافتتاحي يُحسب في كشف الحساب', 'يمكن تعطيل المورد بدلاً من حذفه إذا كان مرتبطاً بفواتير'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-address-card"></i> بيانات المورد</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group users-form-group--full">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> الاسم <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror"
                            value="{{ old('name', optional($supplier)->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    @include('admin.components.premium.phone-input', [
                        'name' => 'phone',
                        'value' => old('phone', optional($supplier)->phone),
                        'label' => 'الهاتف',
                    ])

                    <div class="users-form-group">
                        <label for="email" class="users-form-label"><i class="fas fa-envelope"></i> البريد الإلكتروني</label>
                        <input type="email" name="email" id="email" class="users-form-input @error('email') is-invalid @enderror"
                            value="{{ old('email', optional($supplier)->email) }}">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="address" class="users-form-label"><i class="fas fa-map-marker-alt"></i> العنوان</label>
                        <textarea name="address" id="address" class="users-form-textarea" rows="2">{{ old('address', optional($supplier)->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="opening_balance" class="users-form-label"><i class="fas fa-coins"></i> رصيد افتتاحي</label>
                        <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="users-form-input @error('opening_balance') is-invalid @enderror"
                            value="{{ old('opening_balance', optional($supplier)->opening_balance ?? 0) }}">
                        @error('opening_balance')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                        <textarea name="notes" id="notes" class="users-form-textarea" rows="2">{{ old('notes', optional($supplier)->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input"
                                {{ old('is_active', optional($supplier)->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">مورد نشط</span>
                        </label>
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.suppliers.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
