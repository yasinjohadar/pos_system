@php
    $bankAccount = $bankAccount ?? null;
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-landmark',
            'title' => $bankAccount ? 'تعديل حساب بنكي' : 'حساب بنكي جديد',
            'text' => 'سجّل حسابات البنوك المرتبطة بالفروع لمتابعة الأرصدة والتحويلات والشيكات.',
            'tips' => ['رقم الحساب اختياري لكنه مفيد للمراجعة', 'الرصيد الافتتاحي يُسجّل عند الإنشاء', 'اربط الحساب بفرع إن كان مخصصاً له'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-university"></i> بيانات الحساب البنكي</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group users-form-group--full">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> اسم البنك <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror"
                            value="{{ old('name', optional($bankAccount)->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="account_number" class="users-form-label"><i class="fas fa-hashtag"></i> رقم الحساب</label>
                        <input type="text" name="account_number" id="account_number" class="users-form-input @error('account_number') is-invalid @enderror"
                            value="{{ old('account_number', optional($bankAccount)->account_number) }}" dir="ltr">
                        @error('account_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="branch_id" class="users-form-label"><i class="fas fa-building"></i> الفرع</label>
                        <select name="branch_id" id="branch_id" class="users-form-select @error('branch_id') is-invalid @enderror">
                            <option value="">— لا يوجد —</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', optional($bankAccount)->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="opening_balance" class="users-form-label"><i class="fas fa-coins"></i> رصيد افتتاحي</label>
                        <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="users-form-input @error('opening_balance') is-invalid @enderror"
                            value="{{ old('opening_balance', optional($bankAccount)->opening_balance ?? 0) }}">
                        @error('opening_balance')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="currency" class="users-form-label"><i class="fas fa-money-bill-wave"></i> العملة</label>
                        <input type="text" name="currency" id="currency" class="users-form-input @error('currency') is-invalid @enderror"
                            value="{{ old('currency', optional($bankAccount)->currency) }}" placeholder="مثال: SAR">
                        @error('currency')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                        <textarea name="notes" id="notes" class="users-form-textarea" rows="2">{{ old('notes', optional($bankAccount)->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input"
                                {{ old('is_active', optional($bankAccount)->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">نشط</span>
                        </label>
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.bank-accounts.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
