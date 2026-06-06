@php
    $name = $name ?? 'supplier_id';
    $id = $id ?? $name;
    $selected = $selected ?? null;
    $placeholder = $placeholder ?? 'ابحث بالاسم أو الهاتف...';
    $required = $required ?? false;
@endphp
<select
    name="{{ $name }}"
    id="{{ $id }}"
    class="users-form-select users-supplier-search"
    data-placeholder="{{ $placeholder }}"
    @if ($required) required @endif
>
    <option value=""></option>
    @if ($selected)
        <option value="{{ $selected->id }}" selected>
            {{ $selected->name }}{{ $selected->phone ? ' (' . $selected->phone . ')' : '' }}
        </option>
    @endif
</select>
