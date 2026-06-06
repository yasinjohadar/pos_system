<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-file-archive"></i> بيانات النسخة</h6>
    <div class="users-form-grid">
        <div class="users-form-group users-form-group--full">
            <label for="name" class="users-form-label">
                <i class="fas fa-tag"></i>
                اسم النسخة
                <span class="users-form-required">*</span>
            </label>
            <input type="text" class="users-form-input @error('name') is-invalid @enderror"
                id="name" name="name"
                value="{{ old('name', 'backup_' . now()->format('Y-m-d_H-i-s')) }}" required>
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
                    <option value="{{ $key }}" {{ old('backup_type', 'database') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('backup_type')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="compression_type" class="users-form-label">
                <i class="fas fa-file-zipper"></i>
                نوع الضغط
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('compression_type') is-invalid @enderror"
                id="compression_type" name="compression_type" required>
                @foreach ($compressionTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('compression_type', 'zip') == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('compression_type')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="storage_config_id" class="users-form-label">
                <i class="fas fa-hdd"></i>
                مكان التخزين
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('storage_config_id') is-invalid @enderror"
                id="storage_config_id" name="storage_config_id" required>
                <option value="">اختر مكان التخزين</option>
                @foreach ($storageConfigs as $config)
                    <option value="{{ $config->id }}" {{ old('storage_config_id') == $config->id ? 'selected' : '' }}>
                        {{ $config->name }} ({{ \App\Models\AppStorageConfig::DRIVERS[$config->driver] ?? $config->driver }})
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

        <div class="users-form-group">
            <label for="retention_days" class="users-form-label">
                <i class="fas fa-calendar-day"></i>
                أيام الاحتفاظ
                <span class="users-form-required">*</span>
            </label>
            <input type="number" class="users-form-input @error('retention_days') is-invalid @enderror"
                id="retention_days" name="retention_days"
                value="{{ old('retention_days', 30) }}" min="1" max="365" required>
            <span class="users-form-hint">من 1 إلى 365 يوماً</span>
            @error('retention_days')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
