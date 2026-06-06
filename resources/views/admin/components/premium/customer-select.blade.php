@php
    $name = $name ?? 'customer_id';
    $id = $id ?? $name;
    $selected = $selected ?? null;
    $placeholder = $placeholder ?? 'ابحث بالاسم أو الهاتف...';
    $required = $required ?? true;
@endphp
<select
    name="{{ $name }}"
    id="{{ $id }}"
    class="users-form-select users-customer-search"
    data-placeholder="{{ $placeholder }}"
    @if ($required) required @endif
>
    <option value=""></option>
    @if ($selected)
        <option value="{{ $selected->id }}" selected data-loyalty-points="{{ $selected->loyalty_points }}">
            {{ $selected->name }}{{ $selected->phone ? ' (' . $selected->phone . ')' : '' }}
        </option>
    @endif
</select>
