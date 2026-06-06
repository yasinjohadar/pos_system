@php
    $config = $config ?? null;
    $drivers = $drivers ?? [];
    $fileTypes = old('file_types', $config?->file_types ?? []);
    if (! is_array($fileTypes)) {
        $fileTypes = [];
    }
@endphp

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-database"></i> الإعدادات الأساسية</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="name" class="users-form-label">
                <i class="fas fa-tag"></i>
                اسم الإعداد
                <span class="users-form-required">*</span>
            </label>
            <input type="text" class="users-form-input @error('name') is-invalid @enderror"
                id="name" name="name" value="{{ old('name', $config?->name) }}" required>
            @error('name')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="driver" class="users-form-label">
                <i class="fas fa-server"></i>
                نوع التخزين
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('driver') is-invalid @enderror" id="driver" name="driver" required>
                <option value="">اختر نوع التخزين</option>
                @foreach ($drivers as $key => $label)
                    <option value="{{ $key }}" {{ old('driver', $config?->driver) == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('driver')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-sliders-h"></i> إعدادات الاتصال</h6>
    <div id="config-fields" class="users-form-grid"></div>
    @error('config')
        <div class="email-form-alert email-form-alert--warning mt-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ $message }}</span>
        </div>
    @enderror
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-cog"></i> خيارات إضافية</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="priority" class="users-form-label"><i class="fas fa-sort-amount-up"></i> الأولوية</label>
            <input type="number" class="users-form-input" id="priority" name="priority"
                value="{{ old('priority', $config?->priority ?? 0) }}" min="0">
            <span class="users-form-hint">الأعلى يُستخدم أولاً</span>
        </div>

        <div class="users-form-group">
            <label for="cdn_url" class="users-form-label"><i class="fas fa-link"></i> رابط CDN</label>
            <input type="url" class="users-form-input" id="cdn_url" name="cdn_url"
                value="{{ old('cdn_url', $config?->cdn_url) }}" placeholder="https://cdn.example.com" dir="ltr">
        </div>

        <div class="users-form-group users-form-group--full">
            <span class="users-form-label"><i class="fas fa-file"></i> أنواع الملفات المدعومة</span>
            <div class="storage-file-types">
                <label class="storage-file-type">
                    <input type="checkbox" name="file_types[]" value="image" {{ in_array('image', $fileTypes) ? 'checked' : '' }}>
                    <span>صور</span>
                </label>
                <label class="storage-file-type">
                    <input type="checkbox" name="file_types[]" value="document" {{ in_array('document', $fileTypes) ? 'checked' : '' }}>
                    <span>وثائق</span>
                </label>
                <label class="storage-file-type">
                    <input type="checkbox" name="file_types[]" value="video" {{ in_array('video', $fileTypes) ? 'checked' : '' }}>
                    <span>فيديو</span>
                </label>
            </div>
        </div>

        <div class="users-form-group users-form-group--full">
            <div class="users-form-switches">
                <div class="users-form-switch">
                    <div class="users-form-switch__icon users-form-switch__icon--active">
                        <i class="fas fa-circle-check"></i>
                    </div>
                    <div class="users-form-switch__info">
                        <span class="users-form-switch__title">نشط</span>
                        <span class="users-form-switch__desc">أماكن التخزين غير النشطة لا تُستخدم</span>
                    </div>
                    <label class="users-toggle users-toggle--compact">
                        <input type="checkbox" class="users-toggle-input users-form-toggle-input" id="is_active"
                            name="is_active" value="1" data-label-on="نشط" data-label-off="غير نشط"
                            {{ old('is_active', $config?->is_active ?? true) ? 'checked' : '' }}>
                        <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                        <span class="users-toggle-label">{{ old('is_active', $config?->is_active ?? true) ? 'نشط' : 'غير نشط' }}</span>
                    </label>
                </div>
                <div class="users-form-switch">
                    <div class="users-form-switch__icon">
                        <i class="fas fa-clone"></i>
                    </div>
                    <div class="users-form-switch__info">
                        <span class="users-form-switch__title">التخزين المتعدد</span>
                        <span class="users-form-switch__desc">Redundancy — نسخ الملفات على أكثر من مزود</span>
                    </div>
                    <label class="users-toggle users-toggle--compact">
                        <input type="checkbox" class="users-toggle-input users-form-toggle-input" id="redundancy"
                            name="redundancy" value="1" data-label-on="مفعّل" data-label-off="معطّل"
                            {{ old('redundancy', $config?->redundancy) ? 'checked' : '' }}>
                        <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                        <span class="users-toggle-label">{{ old('redundancy', $config?->redundancy) ? 'مفعّل' : 'معطّل' }}</span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-coins"></i> إعدادات التسعير (اختياري)</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label class="users-form-label">تكلفة التخزين / GB</label>
            <input type="number" step="0.01" class="users-form-input" name="pricing_config[storage_cost_per_gb]"
                value="{{ old('pricing_config.storage_cost_per_gb', $config?->pricing_config['storage_cost_per_gb'] ?? '') }}">
        </div>
        <div class="users-form-group">
            <label class="users-form-label">تكلفة الرفع / GB</label>
            <input type="number" step="0.01" class="users-form-input" name="pricing_config[upload_cost_per_gb]"
                value="{{ old('pricing_config.upload_cost_per_gb', $config?->pricing_config['upload_cost_per_gb'] ?? '') }}">
        </div>
        <div class="users-form-group">
            <label class="users-form-label">تكلفة التحميل / GB</label>
            <input type="number" step="0.01" class="users-form-input" name="pricing_config[download_cost_per_gb]"
                value="{{ old('pricing_config.download_cost_per_gb', $config?->pricing_config['download_cost_per_gb'] ?? '') }}">
        </div>
        <div class="users-form-group">
            <label class="users-form-label">الميزانية الشهرية</label>
            <input type="number" step="0.01" class="users-form-input" name="monthly_budget"
                value="{{ old('monthly_budget', $config?->monthly_budget) }}">
        </div>
        <div class="users-form-group">
            <label class="users-form-label">حد تنبيه التكلفة</label>
            <input type="number" step="0.01" class="users-form-input" name="cost_alert_threshold"
                value="{{ old('cost_alert_threshold', $config?->cost_alert_threshold) }}">
        </div>
    </div>
</div>

<div id="test-connection-result" class="storage-test-result" hidden></div>
