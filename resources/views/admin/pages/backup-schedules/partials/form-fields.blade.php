@php
    use App\Models\AppStorageConfig;
    use App\Models\BackupSchedule;

    $schedule = ($schedule ?? null) instanceof BackupSchedule ? $schedule : null;
    $backupTypes = $backupTypes ?? [];
    $frequencies = $frequencies ?? [];
    $storageConfigs = $storageConfigs ?? collect();
    $compressionTypes = $compressionTypes ?? [];

    $selectedStorageConfigId = old('storage_config_id');
    if ($selectedStorageConfigId === null && $schedule?->storage_drivers && count($schedule->storage_drivers) > 0) {
        $matchedConfig = AppStorageConfig::where('driver', $schedule->storage_drivers[0])
            ->where('is_active', true)
            ->first();
        $selectedStorageConfigId = $matchedConfig?->id;
    }

    $daysOfWeek = old('days_of_week', $schedule?->days_of_week ?? []);
    if (! is_array($daysOfWeek)) {
        $daysOfWeek = [];
    }

    $compressionSelected = old('compression_types', $schedule?->compression_types ?? ['zip']);
    if (! is_array($compressionSelected)) {
        $compressionSelected = ['zip'];
    }

    $weekDays = [0 => 'الأحد', 1 => 'الإثنين', 2 => 'الثلاثاء', 3 => 'الأربعاء', 4 => 'الخميس', 5 => 'الجمعة', 6 => 'السبت'];
    $initialFrequency = old('frequency', $schedule?->frequency ?? 'daily');
@endphp

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-calendar-alt"></i> بيانات الجدولة</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="name" class="users-form-label">
                <i class="fas fa-tag"></i>
                اسم الجدولة
                <span class="users-form-required">*</span>
            </label>
            <input type="text" class="users-form-input @error('name') is-invalid @enderror"
                id="name" name="name" value="{{ old('name', $schedule?->name) }}" required>
            @error('name')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="backup_type" class="users-form-label">
                <i class="fas fa-database"></i>
                نوع النسخ
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('backup_type') is-invalid @enderror"
                id="backup_type" name="backup_type" required>
                @foreach ($backupTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('backup_type', $schedule?->backup_type ?? 'full') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('backup_type')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="frequency" class="users-form-label">
                <i class="fas fa-redo"></i>
                التكرار
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('frequency') is-invalid @enderror"
                id="frequency" name="frequency" required>
                @foreach ($frequencies as $key => $label)
                    <option value="{{ $key }}" {{ $initialFrequency == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('frequency')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="time" class="users-form-label">
                <i class="fas fa-clock"></i>
                الوقت
                <span class="users-form-required">*</span>
            </label>
            <input type="time" class="users-form-input @error('time') is-invalid @enderror"
                id="time" name="time" value="{{ old('time', $schedule?->time ?? '02:00') }}" required dir="ltr">
            @error('time')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group" id="day_of_month_field" @if($initialFrequency !== 'monthly') hidden @endif>
            <label for="day_of_month" class="users-form-label">
                <i class="fas fa-calendar-day"></i>
                يوم الشهر
            </label>
            <input type="number" class="users-form-input" id="day_of_month" name="day_of_month"
                value="{{ old('day_of_month', $schedule?->day_of_month ?? 1) }}" min="1" max="31">
            <span class="users-form-hint">من 1 إلى 31</span>
        </div>
    </div>
</div>

<div class="users-form-card__section" id="days_of_week_field" @if($initialFrequency !== 'weekly') hidden @endif>
    <h6 class="users-form-section-title"><i class="fas fa-calendar-week"></i> أيام الأسبوع</h6>
    <div class="storage-file-types">
        @foreach ($weekDays as $day => $label)
            <label class="storage-file-type">
                <input type="checkbox" name="days_of_week[]" value="{{ $day }}"
                    {{ in_array($day, $daysOfWeek) ? 'checked' : '' }}>
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-cog"></i> إعدادات النسخ</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="storage_config_id" class="users-form-label">
                <i class="fas fa-hdd"></i>
                مكان التخزين
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('storage_config_id') is-invalid @enderror"
                id="storage_config_id" name="storage_config_id" required>
                <option value="">اختر مكان التخزين</option>
                @foreach ($storageConfigs as $config)
                    <option value="{{ $config->id }}" {{ (string) $selectedStorageConfigId === (string) $config->id ? 'selected' : '' }}>
                        {{ $config->name }} ({{ AppStorageConfig::DRIVERS[$config->driver] ?? $config->driver }})
                    </option>
                @endforeach
            </select>
            @if ($storageConfigs->isEmpty())
                <span class="users-form-hint users-form-hint--danger">
                    لا توجد أماكن تخزين نشطة.
                    <a href="{{ route('admin.storage.create') }}">أضف مكان تخزين</a> أولاً.
                </span>
            @endif
            @error('storage_config_id')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full">
            <span class="users-form-label">
                <i class="fas fa-file-zipper"></i>
                أنواع الضغط
                <span class="users-form-required">*</span>
            </span>
            <div class="storage-file-types">
                @foreach ($compressionTypes as $key => $label)
                    <label class="storage-file-type">
                        <input type="checkbox" class="@error('compression_types') is-invalid @enderror"
                            name="compression_types[]" value="{{ $key }}"
                            {{ in_array($key, $compressionSelected) ? 'checked' : '' }}>
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('compression_types')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="retention_days" class="users-form-label">
                <i class="fas fa-hourglass-half"></i>
                أيام الاحتفاظ
                <span class="users-form-required">*</span>
            </label>
            <input type="number" class="users-form-input @error('retention_days') is-invalid @enderror"
                id="retention_days" name="retention_days"
                value="{{ old('retention_days', $schedule?->retention_days ?? 30) }}" min="1" max="365" required>
            <span class="users-form-hint">من 1 إلى 365 يوماً</span>
            @error('retention_days')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
