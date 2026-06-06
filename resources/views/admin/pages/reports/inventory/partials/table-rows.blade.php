@forelse ($rows as $row)
    @php
        $qty = (float) $row->quantity;
        $qtyClass = $qty < 0 ? 'users-qty--out' : ($qty == 0 ? 'users-muted-text' : 'users-qty--in');
    @endphp
    <tr>
        <td>
            <span class="users-user-name" style="cursor: default;">{{ $row->product->name ?? '—' }}</span>
            @if ($row->product?->barcode)
                <small class="users-muted-text d-block" dir="ltr">{{ $row->product->barcode }}</small>
            @endif
        </td>
        <td>
            @if ($row->product?->category)
                <span class="users-badge users-badge--role">{{ $row->product->category->name }}</span>
            @else
                —
            @endif
        </td>
        <td>{{ $row->warehouse->name ?? '—' }}</td>
        <td><span class="users-amount {{ $qtyClass }}">{{ number_format($qty, 4) }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="users-empty">لا توجد بيانات — جرّب تغيير الفلاتر أو شغّل <code>StockSeeder</code></td>
    </tr>
@endforelse

@if (count($rows) > 0)
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td colspan="3" class="text-end" style="padding: 0.875rem 1rem;">عدد السجلات:</td>
        <td><span class="users-amount">{{ count($rows) }}</span></td>
    </tr>
@endif
