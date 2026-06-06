@php
    $nameValue = old('name', $product?->name);
    $categoryValue = old('category_id', $product?->category_id);
    $unitValue = old('unit_id', $product?->unit_id);
    $barcodeValue = old('barcode', $product?->barcode);
    $descriptionValue = old('description', $product?->description);
    $basePriceValue = old('base_price', $product?->base_price ?? 0);
    $costPriceValue = old('cost_price', $product?->cost_price);
    $minStockValue = old('min_stock_alert', $product?->min_stock_alert ?? 0);
    $reorderValue = old('reorder_level', $product?->reorder_level);
    $maxLevelValue = old('max_level', $product?->max_level);
    $taxValue = old('tax_id', $product?->tax_id);
    $isActiveChecked = (bool) old('is_active', $product?->is_active ?? true);
@endphp

<div class="users-form-grid">
    <div class="users-form-group users-form-group--full">
        <label for="name" class="users-form-label">
            <i class="fas fa-box"></i>
            اسم المنتج
            <span class="users-form-required">*</span>
        </label>
        <input type="text"
            class="users-form-input @error('name') is-invalid @enderror"
            id="name"
            name="name"
            value="{{ $nameValue }}"
            required>
        @error('name')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="category_id" class="users-form-label">
            <i class="fas fa-tags"></i>
            التصنيف
        </label>
        <select class="users-form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
            <option value="">— اختر —</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" {{ (string) $categoryValue === (string) $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="unit_id" class="users-form-label">
            <i class="fas fa-ruler-combined"></i>
            الوحدة
        </label>
        <select class="users-form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id">
            <option value="">— اختر —</option>
            @foreach ($units as $u)
                <option value="{{ $u->id }}" {{ (string) $unitValue === (string) $u->id ? 'selected' : '' }}>
                    {{ $u->name }}{{ $u->symbol ? ' (' . $u->symbol . ')' : '' }}
                </option>
            @endforeach
        </select>
        @error('unit_id')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group users-form-group--full">
        <label for="barcode" class="users-form-label">
            <i class="fas fa-barcode"></i>
            الباركود
        </label>
        <input type="text"
            class="users-form-input @error('barcode') is-invalid @enderror"
            id="barcode"
            name="barcode"
            value="{{ $barcodeValue }}"
            placeholder="للبحث في نقطة البيع"
            dir="ltr">
        @error('barcode')
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
            rows="3">{{ $descriptionValue }}</textarea>
        @error('description')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="base_price" class="users-form-label">
            <i class="fas fa-tag"></i>
            السعر الأساسي
            <span class="users-form-required">*</span>
        </label>
        <input type="number"
            step="0.01"
            min="0"
            class="users-form-input @error('base_price') is-invalid @enderror"
            id="base_price"
            name="base_price"
            value="{{ $basePriceValue }}"
            required>
        @error('base_price')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="cost_price" class="users-form-label">
            <i class="fas fa-coins"></i>
            سعر التكلفة
        </label>
        <input type="number"
            step="0.01"
            min="0"
            class="users-form-input @error('cost_price') is-invalid @enderror"
            id="cost_price"
            name="cost_price"
            value="{{ $costPriceValue }}">
        @error('cost_price')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    @isset($taxes)
    <div class="users-form-group">
        <label for="tax_id" class="users-form-label">
            <i class="fas fa-percent"></i>
            الضريبة الافتراضية
        </label>
        <select class="users-form-select @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id">
            <option value="">— بدون —</option>
            @foreach ($taxes as $tax)
                <option value="{{ $tax->id }}" {{ (string) $taxValue === (string) $tax->id ? 'selected' : '' }}>
                    {{ $tax->name }} ({{ $tax->type === 'percent' ? $tax->rate . '%' : $tax->rate }})
                </option>
            @endforeach
        </select>
        @error('tax_id')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>
    @endisset

    <div class="users-form-group">
        <label for="min_stock_alert" class="users-form-label">
            <i class="fas fa-bell"></i>
            حد تنبيه المخزون
        </label>
        <input type="number"
            min="0"
            class="users-form-input @error('min_stock_alert') is-invalid @enderror"
            id="min_stock_alert"
            name="min_stock_alert"
            value="{{ $minStockValue }}">
        @error('min_stock_alert')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="reorder_level" class="users-form-label">
            <i class="fas fa-arrow-rotate-right"></i>
            حد إعادة الطلب
        </label>
        <input type="number"
            step="0.01"
            min="0"
            class="users-form-input @error('reorder_level') is-invalid @enderror"
            id="reorder_level"
            name="reorder_level"
            value="{{ $reorderValue }}">
        @error('reorder_level')
            <div class="users-form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="users-form-group">
        <label for="max_level" class="users-form-label">
            <i class="fas fa-layer-group"></i>
            الحد الأقصى للمخزون
        </label>
        <input type="number"
            step="0.01"
            min="0"
            class="users-form-input @error('max_level') is-invalid @enderror"
            id="max_level"
            name="max_level"
            value="{{ $maxLevelValue }}">
        @error('max_level')
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
        @if ($product?->image)
            <div class="users-form-image-preview">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                <span>{{ basename($product->image) }}</span>
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
            <span class="users-form-switch__title">منتج نشط</span>
            <span class="users-form-switch__desc">المنتجات غير النشطة لا تظهر في نقطة البيع والقوائم</span>
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
