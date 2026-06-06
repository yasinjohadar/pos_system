@php
    $name = $name ?? 'product_id';
    $id = $id ?? $name;
    $selected = $selected ?? null;
    $placeholder = $placeholder ?? 'ابحث بالاسم أو الباركود...';
    $required = $required ?? true;
@endphp
<select
    name="{{ $name }}"
    id="{{ $id }}"
    class="users-form-select users-product-search"
    data-placeholder="{{ $placeholder }}"
    @if ($required) required @endif
>
    <option value=""></option>
    @if ($selected)
        <option value="{{ $selected->id }}" selected>
            {{ $selected->name }}{{ $selected->barcode ? ' (' . $selected->barcode . ')' : '' }}
        </option>
    @endif
</select>
