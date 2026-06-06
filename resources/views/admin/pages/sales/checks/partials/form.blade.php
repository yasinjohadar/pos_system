<form action="{{ $action }}" method="POST" id="check-form">
    @csrf

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-money-check',
            'title' => 'شيك جديد',
            'text' => 'سجّل شيكاً وارداً أو صادراً لمتابعة التحصيل وتاريخ الاستحقاق. يبدأ الشيك بحالة «تحت التحصيل».',
            'tips' => ['اربط الشيك بحساب بنكي إن وُجد', 'أو أدخل اسم البنك يدوياً', 'حدّث الحالة لاحقاً من صفحة التفاصيل'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-money-check-alt"></i> بيانات الشيك</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="check_number" class="users-form-label"><i class="fas fa-hashtag"></i> رقم الشيك <span class="users-form-required">*</span></label>
                        <input type="text" name="check_number" id="check_number" class="users-form-input @error('check_number') is-invalid @enderror"
                            value="{{ old('check_number') }}" required>
                        @error('check_number')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="amount" class="users-form-label"><i class="fas fa-coins"></i> المبلغ <span class="users-form-required">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount" class="users-form-input @error('amount') is-invalid @enderror"
                            value="{{ old('amount') }}" required min="0.01">
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="bank_account_id" class="users-form-label"><i class="fas fa-landmark"></i> الحساب البنكي</label>
                        <select name="bank_account_id" id="bank_account_id" class="users-form-select @error('bank_account_id') is-invalid @enderror">
                            <option value="">— اختياري —</option>
                            @foreach ($bankAccounts as $ba)
                                <option value="{{ $ba->id }}" {{ old('bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group" id="bank_name_wrap">
                        <label for="bank_name" class="users-form-label"><i class="fas fa-university"></i> اسم البنك</label>
                        <input type="text" name="bank_name" id="bank_name" class="users-form-input @error('bank_name') is-invalid @enderror"
                            value="{{ old('bank_name') }}" placeholder="إن لم يكن مرتبطاً بحساب">
                        @error('bank_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="due_date" class="users-form-label"><i class="fas fa-calendar"></i> تاريخ الاستحقاق <span class="users-form-required">*</span></label>
                        <input type="date" name="due_date" id="due_date" class="users-form-input @error('due_date') is-invalid @enderror"
                            value="{{ old('due_date') }}" required>
                        @error('due_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                        <textarea name="notes" id="notes" class="users-form-textarea" rows="2">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.checks.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
