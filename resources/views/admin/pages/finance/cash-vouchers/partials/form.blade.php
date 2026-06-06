<form action="{{ $action }}" method="POST" id="cash-voucher-form">
    @csrf

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-file-invoice-dollar',
            'title' => 'سند قبض / صرف جديد',
            'text' => 'سجّل حركة نقدية واردة (قبض) أو صادرة (صرف) على خزنة أو حساب بنكي.',
            'tips' => ['اختر خزنة أو حساب بنكي واحد على الأقل', 'يُنشأ رقم السند تلقائياً', 'يُسجّل قيد محاسبي إن وُجدت الحسابات'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-receipt"></i> بيانات السند</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="type" class="users-form-label"><i class="fas fa-exchange-alt"></i> النوع <span class="users-form-required">*</span></label>
                        <select name="type" id="type" class="users-form-select @error('type') is-invalid @enderror" required>
                            <option value="receipt" {{ old('type', 'receipt') === 'payment' ? '' : 'selected' }}>قبض</option>
                            <option value="payment" {{ old('type') === 'payment' ? 'selected' : '' }}>صرف</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="date" class="users-form-label"><i class="fas fa-calendar"></i> التاريخ <span class="users-form-required">*</span></label>
                        <input type="date" name="date" id="date" class="users-form-input @error('date') is-invalid @enderror"
                            value="{{ old('date', date('Y-m-d')) }}" required>
                        @error('date')
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
                        <label for="treasury_id" class="users-form-label"><i class="fas fa-piggy-bank"></i> الخزنة</label>
                        <select name="treasury_id" id="treasury_id" class="users-form-select @error('treasury_id') is-invalid @enderror">
                            <option value="">— اختياري —</option>
                            @foreach ($treasuries as $t)
                                <option value="{{ $t->id }}" {{ old('treasury_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }})
                                </option>
                            @endforeach
                        </select>
                        @error('treasury_id')
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

                    <div class="users-form-group">
                        <label for="currency" class="users-form-label"><i class="fas fa-money-bill-wave"></i> العملة</label>
                        <input type="text" name="currency" id="currency" class="users-form-input @error('currency') is-invalid @enderror"
                            value="{{ old('currency', 'SAR') }}" placeholder="مثال: SAR">
                        @error('currency')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="category" class="users-form-label"><i class="fas fa-tag"></i> الفئة</label>
                        <input type="text" name="category" id="category" class="users-form-input @error('category') is-invalid @enderror"
                            value="{{ old('category') }}" placeholder="مصروف، إيراد آخر...">
                        @error('category')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="description" class="users-form-label"><i class="fas fa-align-right"></i> البيان</label>
                        <input type="text" name="description" id="description" class="users-form-input @error('description') is-invalid @enderror"
                            value="{{ old('description') }}">
                        @error('description')
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
                    <a href="{{ route('admin.cash-vouchers.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
