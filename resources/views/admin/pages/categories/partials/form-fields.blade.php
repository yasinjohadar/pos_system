@php
    $nameValue = old('name', $category?->name);
    $parentValue = old('parent_id', $category?->parent_id);
    $descriptionValue = old('description', $category?->description);
    $orderValue = old('order', $category?->order ?? 0);
    $isActiveChecked = (bool) old('is_active', $category?->is_active ?? true);
@endphp

<div class="users-form-grid">
    <div class="users-form-group users-form-group--full">
        <label for="name" class="users-form-label">
            <i class="fas fa-tag"></i>
            الاسم
            <span class="users-form-required">*</span>
        </label>
        <input type="text"
            class="users-form-input @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ $nameValue }}"
            placeholder="مثال: أدوات منزلية"
            required>
        @error('name')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="parent_id" class="users-form-label">
            <i class="fas fa-sitemap"></i>
            التصنيف الأب
        </label>
        <select class="users-form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
            <option value="">— لا يوجد —</option>
            @foreach ($parentCategories as $c)
                <option value="{{ $c->id }}" {{ (string) $parentValue === (string) $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        <span class="users-form-hint">اتركه فارغاً لتصنيف رئيسي</span>
        @error('parent_id')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="order" class="users-form-label">
            <i class="fas fa-sort-numeric-down"></i>
            الترتيب
        </label>
        <input type="number"
            class="users-form-input @error('order') is-invalid @enderror"
            id="order"
            name="order"
            value="{{ $orderValue }}"
            min="0">
        @error('order')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group users-form-group--full">
        <label for="description" class="users-form-label">
            <i class="fas fa-align-right"></i>
            الوصف
        </label>
        <textarea class="users-form-textarea @error('description') is-invalid @enderror"
            id="description"
            name="description"
            rows="3"
            placeholder="وصف مختصر للتصنيف">{{ $descriptionValue }}</textarea>
        @error('description')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group users-form-group--full">
        <label for="image" class="users-form-label">
            <i class="fas fa-image"></i>
            الصورة
        </label>
        <input type="file"
            class="users-form-input @error('image') is-invalid @enderror"
            id="image"
            name="image"
            accept="image/*">
        @if ($category?->image)
            <div class="users-form-image-preview">
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                <span>{{ basename($category->image) }}</span>
            </div>
        @endif
        @error('image')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="users-form-switches">
    <div class="users-form-switch">
        <div class="users-form-switch__icon users-form-switch__icon--active">
            <i class="fas fa-circle-check"></i>
        </div>
        <div class="users-form-switch__info">
            <span class="users-form-switch__title">تصنيف نشط</span>
            <span class="users-form-switch__desc">التصنيفات غير النشطة لا تظهر في قوائم الاختيار</span>
        </div>
        <label class="users-toggle users-toggle--compact">
            <input type="checkbox"
                class="users-toggle-input users-form-toggle-input"
                id="is_active"
                name="is_active"
                value="1"
                data-label-on="نشط"
                data-label-off="غير نشط"
                {{ $isActiveChecked ? 'checked' : '' }}>
            <span class="users-toggle-track">
                <span class="users-toggle-thumb"></span>
            </span>
            <span class="users-toggle-label">{{ $isActiveChecked ? 'نشط' : 'غير نشط' }}</span>
        </label>
    </div>
</div>
