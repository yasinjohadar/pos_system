@php
    use App\Models\AppStorageConfig;
    use App\Models\StorageDiskMapping;

    $mapping = ($mapping ?? null) instanceof StorageDiskMapping ? $mapping : null;
    $storages = $storages ?? collect();

    $fallbackIds = old('fallback_storage_ids', $mapping?->fallback_storage_ids ?? []);
    if (! is_array($fallbackIds)) {
        $fallbackIds = [];
    }

    $fileTypes = old('file_types', $mapping?->file_types ?? []);
    if (! is_array($fileTypes)) {
        $fileTypes = [];
    }
@endphp

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-link"></i> بيانات الربط</h6>
    <div class="users-form-grid">
        <div class="users-form-group">
            <label for="disk_name" class="users-form-label">
                <i class="fas fa-code"></i>
                Disk Name
                <span class="users-form-required">*</span>
            </label>
            <input type="text" class="users-form-input @error('disk_name') is-invalid @enderror"
                id="disk_name" name="disk_name" value="{{ old('disk_name', $mapping?->disk_name) }}"
                required placeholder="images, documents, videos..." dir="ltr">
            <span class="users-form-hint">مثال: images, documents, videos, attachments</span>
            @error('disk_name')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group">
            <label for="label" class="users-form-label">
                <i class="fas fa-tag"></i>
                التسمية
                <span class="users-form-required">*</span>
            </label>
            <input type="text" class="users-form-input @error('label') is-invalid @enderror"
                id="label" name="label" value="{{ old('label', $mapping?->label) }}" required>
            @error('label')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="users-form-group users-form-group--full">
            <label for="primary_storage_id" class="users-form-label">
                <i class="fas fa-hdd"></i>
                التخزين الأساسي
                <span class="users-form-required">*</span>
            </label>
            <select class="users-form-select @error('primary_storage_id') is-invalid @enderror"
                id="primary_storage_id" name="primary_storage_id" required>
                <option value="">اختر التخزين الأساسي</option>
                @foreach ($storages as $storage)
                    <option value="{{ $storage->id }}"
                        {{ (string) old('primary_storage_id', $mapping?->primary_storage_id) === (string) $storage->id ? 'selected' : '' }}>
                        {{ $storage->name }} ({{ AppStorageConfig::DRIVERS[$storage->driver] ?? $storage->driver }})
                    </option>
                @endforeach
            </select>
            @if ($storages->isEmpty())
                <span class="users-form-hint users-form-hint--danger">
                    لا توجد أماكن تخزين نشطة.
                    <a href="{{ route('admin.storage.create') }}">أضف مكان تخزين</a> أولاً.
                </span>
            @endif
            @error('primary_storage_id')
                <div class="users-form-error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-clone"></i> التخزين الاحتياطي (Fallback)</h6>
    @if ($storages->isEmpty())
        <div class="email-form-alert">
            <i class="fas fa-info-circle"></i>
            <span>لا توجد أماكن تخزين متاحة للاختيار.</span>
        </div>
    @else
        <div class="storage-file-types">
            @foreach ($storages as $storage)
                <label class="storage-file-type">
                    <input type="checkbox" name="fallback_storage_ids[]" value="{{ $storage->id }}"
                        {{ in_array($storage->id, $fallbackIds) ? 'checked' : '' }}>
                    <span>{{ $storage->name }} ({{ AppStorageConfig::DRIVERS[$storage->driver] ?? $storage->driver }})</span>
                </label>
            @endforeach
        </div>
    @endif
</div>

<div class="users-form-card__section">
    <h6 class="users-form-section-title"><i class="fas fa-file"></i> أنواع الملفات (اختياري)</h6>
    <div class="storage-file-types">
        <label class="storage-file-type">
            <input type="checkbox" name="file_types[]" value="image"
                {{ in_array('image', $fileTypes) ? 'checked' : '' }}>
            <span>صور</span>
        </label>
        <label class="storage-file-type">
            <input type="checkbox" name="file_types[]" value="document"
                {{ in_array('document', $fileTypes) ? 'checked' : '' }}>
            <span>وثائق</span>
        </label>
        <label class="storage-file-type">
            <input type="checkbox" name="file_types[]" value="video"
                {{ in_array('video', $fileTypes) ? 'checked' : '' }}>
            <span>فيديو</span>
        </label>
    </div>
</div>

<div class="users-form-card__section">
    <div class="users-form-switches">
        <div class="users-form-switch">
            <div class="users-form-switch__icon users-form-switch__icon--active">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="users-form-switch__info">
                <span class="users-form-switch__title">نشط</span>
                <span class="users-form-switch__desc">الربط غير النشط لا يُستخدم في النظام</span>
            </div>
            <label class="users-toggle users-toggle--compact">
                <input type="checkbox" class="users-toggle-input users-form-toggle-input" id="is_active"
                    name="is_active" value="1" data-label-on="نشط" data-label-off="غير نشط"
                    {{ old('is_active', $mapping?->is_active ?? true) ? 'checked' : '' }}>
                <span class="users-toggle-track"><span class="users-toggle-thumb"></span></span>
                <span class="users-toggle-label">{{ old('is_active', $mapping?->is_active ?? true) ? 'نشط' : 'غير نشط' }}</span>
            </label>
        </div>
    </div>
</div>
