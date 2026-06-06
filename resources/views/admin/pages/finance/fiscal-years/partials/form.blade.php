<form action="{{ $action }}" method="POST">
    @csrf

    <div class="users-form-layout">
        @include('admin.components.premium.form-aside', [
            'icon' => 'fa-calendar-alt',
            'title' => 'سنة مالية جديدة',
            'text' => 'عرّف فترة محاسبية جديدة. تُربط الفواتير والسندات تلقائياً بالسنة النشطة حسب التاريخ.',
            'tips' => ['لا يجب تداخل الفترات', 'السنة الجديدة تُنشأ نشطة', 'يمكن إقفال السنة لاحقاً من القائمة'],
        ])

        <div class="users-form-card">
            <div class="users-form-card__header">
                <h6 class="users-form-card__title"><i class="fas fa-calendar"></i> بيانات السنة المالية</h6>
            </div>
            <div class="users-form-card__body">
                <div class="users-form-grid">
                    <div class="users-form-group users-form-group--full">
                        <label for="name" class="users-form-label"><i class="fas fa-font"></i> الاسم <span class="users-form-required">*</span></label>
                        <input type="text" name="name" id="name" class="users-form-input @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required placeholder="مثال: السنة المالية 2026">
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="start_date" class="users-form-label"><i class="fas fa-calendar-day"></i> من تاريخ <span class="users-form-required">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="users-form-input @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date') }}" required>
                        @error('start_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="users-form-group">
                        <label for="end_date" class="users-form-label"><i class="fas fa-calendar-check"></i> إلى تاريخ <span class="users-form-required">*</span></label>
                        <input type="date" name="end_date" id="end_date" class="users-form-input @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date') }}" required>
                        @error('end_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="users-form-actions">
                    <button type="submit" class="users-btn-submit"><i class="fas fa-save"></i> {{ $submitLabel ?? 'حفظ' }}</button>
                    <a href="{{ route('admin.fiscal-years.index') }}" class="users-btn-secondary">إلغاء</a>
                </div>
            </div>
        </div>
    </div>
</form>
