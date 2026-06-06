@php
    $treasury = $treasury ?? null;
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-piggy-bank',
            'title' => $treasury ? 'تعديل خزنة / بنك' : 'خزنة / بنك جديد',
            'text' => 'أدر الخزائن النقدية وحسابات البنوك المرتبطة بالفروع. تُستخدم عند تسجيل دفعات الفواتير والتحويلات المالية.',
            'tips' => ['خزنة = نقدي في الفرع', 'بنك = حساب بنكي للتحويلات', 'الرصيد الافتتاحي يُسجّل عند الإنشاء فقط'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-university"></i> بيانات الخزنة / البنك</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group users-form-group--full">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> الاسم <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror"
                            value="{{ old('name', optional($treasury)->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="type" class="users-form-label"><i class="fas fa-list"></i> النوع <span class="users-form-required">*</span></label>
                        <select name="type" id="type" class="users-form-select @error('type') is-invalid @enderror" required>
                            <option value="cashbox" {{ old('type', optional($treasury)->type ?? 'cashbox') === 'cashbox' ? 'selected' : '' }}>خزنة</option>
                            <option value="bank" {{ old('type', optional($treasury)->type) === 'bank' ? 'selected' : '' }}>بنك</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="branch_id" class="users-form-label"><i class="fas fa-building"></i> الفرع</label>
                        <select name="branch_id" id="branch_id" class="users-form-select @error('branch_id') is-invalid @enderror">
                            <option value="">— لا يوجد —</option>
                            @foreach ($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', optional($treasury)->branch_id) == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="opening_balance" class="users-form-label"><i class="fas fa-coins"></i> رصيد افتتاحي</label>
                        <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="users-form-input @error('opening_balance') is-invalid @enderror"
                            value="{{ old('opening_balance', optional($treasury)->opening_balance ?? 0) }}">
                        @error('opening_balance')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="currency" class="users-form-label"><i class="fas fa-money-bill-wave"></i> العملة</label>
                        <input type="text" name="currency" id="currency" class="users-form-input @error('currency') is-invalid @enderror"
                            value="{{ old('currency', optional($treasury)->currency) }}" placeholder="مثال: SAR">
                        @error('currency')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="notes" class="users-form-label"><i class="fas fa-sticky-note"></i> ملاحظات</label>
                        <textarea name="notes" id="notes" class="users-form-textarea" rows="2">{{ old('notes', optional($treasury)->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input"
                                {{ old('is_active', optional($treasury)->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">نشط</span>
                        </label>
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.treasuries.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
