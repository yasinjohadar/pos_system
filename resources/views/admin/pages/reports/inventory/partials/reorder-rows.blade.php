@forelse ($rows as $row)
    @php
        $product = $row->product;
        $totalQty = (float) $row->total_qty;
        $reorderLevel = (float) ($product?->reorder_level ?? 0);
        $isCritical = $totalQty <= $reorderLevel;
    @endphp
    <tr>
        <td>
            <span class="users-user-name" style="cursor: default;">{{ $product?->name ?? '—' }}</span>
        </td>
        <td>
            @if ($product?->category)
                <span class="users-badge users-badge--role">{{ $product->category->name }}</span>
            @else
                —
            @endif
        </td>
        <td>
            <span class="users-amount {{ $isCritical ? 'users-qty--out' : 'users-qty--in' }}">{{ number_format($totalQty, 2) }}</span>
        </td>
        <td><span class="users-amount">{{ number_format($reorderLevel, 2) }}</span></td>
        <td>
            @if ($product?->max_level !== null)
                <span class="users-amount">{{ number_format((float) $product->max_level, 2) }}</span>
            @else
                —
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="users-empty">لا توجد منتجات تحتاج إلى إعادة طلب حالياً</td>
    </tr>
@endforelse

@if (count($rows) > 0)
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td colspan="4" class="text-end" style="padding: 0.875rem 1rem;">عدد المنتجات:</td>
        <td><span class="users-amount">{{ count($rows) }}</span></td>
    </tr>
@endif
