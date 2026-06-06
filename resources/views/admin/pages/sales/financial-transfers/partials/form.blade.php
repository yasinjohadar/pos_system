<form action="{{ $action }}" method="POST" id="financial-transfer-form">
    @csrf

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-exchange-alt',
            'title' => 'تحويل مالي جديد',
            'text' => 'انقل الأموال بين الخزائن والحسابات البنكية. يُسجّل التحويل فوراً في سجل الحركات المالية.',
            'tips' => ['المصدر والوجهة يجب أن يكونا مختلفين', 'خزنة/بنك أو حساب بنكي لكل طرف', 'أضف مرجعاً لتسهيل المطابقة لاحقاً'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-exchange-alt"></i> بيانات التحويل</h6>
            </div>
            <div class="users-form-card__body">
                <h6 class="users-form-section-title"><i class="fas fa-arrow-up"></i> المصدر</h6>
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="from_source" class="users-form-label"><i class="fas fa-list"></i> نوع المصدر <span class="users-form-required">*</span></label>
                        <select name="from_source" id="from_source" class="users-form-select @error('from_source') is-invalid @enderror" required>
                            <option value="treasury" {{ old('from_source', 'treasury') === 'treasury' ? 'selected' : '' }}>خزنة / بنك</option>
                            <option value="bank_account" {{ old('from_source') === 'bank_account' ? 'selected' : '' }}>حساب بنكي</option>
                        </select>
                        @error('from_source')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group" id="from_treasury_wrap">
                        <label for="from_treasury_id" class="users-form-label"><i class="fas fa-piggy-bank"></i> الخزنة / البنك <span class="users-form-required">*</span></label>
                        <select name="from_treasury_id" id="from_treasury_id" class="users-form-select @error('from_treasury_id') is-invalid @enderror">
                            <option value="">— اختر —</option>
                            @foreach ($treasuries as $t)
                                <option value="{{ $t->id }}" {{ old('from_treasury_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})
                                </option>
                            @endforeach
                        </select>
                        @error('from_treasury_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group d-none" id="from_bank_wrap">
                        <label for="from_bank_account_id" class="users-form-label"><i class="fas fa-landmark"></i> الحساب البنكي <span class="users-form-required">*</span></label>
                        <select name="from_bank_account_id" id="from_bank_account_id" class="users-form-select @error('from_bank_account_id') is-invalid @enderror">
                            <option value="">— اختر —</option>
                            @foreach ($bankAccounts as $ba)
                                <option value="{{ $ba->id }}" {{ old('from_bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                            @endforeach
                        </select>
                        @error('from_bank_account_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h6 class="users-form-section-title"><i class="fas fa-arrow-down"></i> الوجهة</h6>
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="to_source" class="users-form-label"><i class="fas fa-list"></i> نوع الوجهة <span class="users-form-required">*</span></label>
                        <select name="to_source" id="to_source" class="users-form-select @error('to_source') is-invalid @enderror" required>
                            <option value="treasury" {{ old('to_source', 'treasury') === 'treasury' ? 'selected' : '' }}>خزنة / بنك</option>
                            <option value="bank_account" {{ old('to_source') === 'bank_account' ? 'selected' : '' }}>حساب بنكي</option>
                        </select>
                        @error('to_source')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group" id="to_treasury_wrap">
                        <label for="to_treasury_id" class="users-form-label"><i class="fas fa-piggy-bank"></i> الخزنة / البنك <span class="users-form-required">*</span></label>
                        <select name="to_treasury_id" id="to_treasury_id" class="users-form-select @error('to_treasury_id') is-invalid @enderror">
                            <option value="">— اختر —</option>
                            @foreach ($treasuries as $t)
                                <option value="{{ $t->id }}" {{ old('to_treasury_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})
                                </option>
                            @endforeach
                        </select>
                        @error('to_treasury_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group d-none" id="to_bank_wrap">
                        <label for="to_bank_account_id" class="users-form-label"><i class="fas fa-landmark"></i> الحساب البنكي <span class="users-form-required">*</span></label>
                        <select name="to_bank_account_id" id="to_bank_account_id" class="users-form-select @error('to_bank_account_id') is-invalid @enderror">
                            <option value="">— اختر —</option>
                            @foreach ($bankAccounts as $ba)
                                <option value="{{ $ba->id }}" {{ old('to_bank_account_id') == $ba->id ? 'selected' : '' }}>{{ $ba->name }}</option>
                            @endforeach
                        </select>
                        @error('to_bank_account_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <h6 class="users-form-section-title"><i class="fas fa-coins"></i> تفاصيل المبلغ</h6>
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="amount" class="users-form-label"><i class="fas fa-coins"></i> المبلغ <span class="users-form-required">*</span></label>
                        <input type="number" step="0.01" name="amount" id="amount" class="users-form-input @error('amount') is-invalid @enderror"
                            value="{{ old('amount') }}" required min="0.01">
                        @error('amount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="transfer_date" class="users-form-label"><i class="fas fa-calendar"></i> تاريخ التحويل <span class="users-form-required">*</span></label>
                        <input type="date" name="transfer_date" id="transfer_date" class="users-form-input @error('transfer_date') is-invalid @enderror"
                            value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                        @error('transfer_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="reference" class="users-form-label"><i class="fas fa-hashtag"></i> مرجع</label>
                        <input type="text" name="reference" id="reference" class="users-form-input @error('reference') is-invalid @enderror"
                            value="{{ old('reference') }}">
                        @error('reference')
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
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'تسجيل التحويل' }}</button>
                    <a href="{{ route('admin.financial-transfers.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
