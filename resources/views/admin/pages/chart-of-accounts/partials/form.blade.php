@php
    $account = $chartOfAccount ?? null;
    $typeOptions = [
        'asset' => 'أصول',
        'liability' => 'خصوم',
        'equity' => 'حقوق ملكية',
        'revenue' => 'إيرادات',
        'expense' => 'مصروفات',
    ];
@endphp

<form action="{{ $action }}" method="POST">
    @csrf
    @if (!empty($method) && $method !== 'POST')
        @method($method)
    @endif

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-sitemap',
            'title' => $account ? 'تعديل حساب' : 'حساب جديد',
            'text' => 'أضف أو عدّل حساباً في شجرة الحسابات. الأكواد الثابتة (1100، 1200...) تُستخدم في القيود التلقائية.',
            'tips' => ['الكود فريد ولا يُكرّر', 'يمكن ربط الحساب بحساب أب', 'لا يُحذف حساب له حركات قيود'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-book"></i> بيانات الحساب</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group">
                        <label for="code" class="users-form-label"><i class="fas fa-hashtag"></i> كود الحساب <span class="users-form-required">*</span></label>
                        <input type="text" name="code" id="code" class="users-form-input @error('code') is-invalid @enderror"
                            value="{{ old('code', optional($account)->code) }}" dir="ltr" required>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> اسم الحساب <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror"
                            value="{{ old('name', optional($account)->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="type" class="users-form-label"><i class="fas fa-list"></i> النوع <span class="users-form-required">*</span></label>
                        <select name="type" id="type" class="users-form-select @error('type') is-invalid @enderror" required>
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('type', optional($account)->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label for="parent_id" class="users-form-label"><i class="fas fa-level-up-alt"></i> الحساب الأب</label>
                        <select name="parent_id" id="parent_id" class="users-form-select @error('parent_id') is-invalid @enderror">
                            <option value="">بدون</option>
                            @foreach ($parents as $p)
                                <option value="{{ $p->id }}" {{ old('parent_id', optional($account)->parent_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->code }} — {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group users-form-group--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" name="is_active" value="1" class="users-form-toggle-input"
                                {{ old('is_active', optional($account)->is_active ?? true) ? 'checked' : '' }}>
                            <span class="users-form-toggle-track"><span class="users-form-toggle-thumb"></span></span>
                            <span class="users-form-toggle-label">الحساب نشط</span>
                        </label>
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.chart-of-accounts.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
