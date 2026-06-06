@forelse ($balances as $b)
    @php
        $isLow = $b->product && $b->product->min_stock_alert > 0 && (float) $b->quantity <= (float) $b->product->min_stock_alert;
    @endphp
    <tr class="{{ $isLow ? 'users-row--warning' : '' }}">
        <th scope="row" class="users-row-index">{{ $balances->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-box"></i></div>
                <span class="users-user-name" style="cursor: default;">{{ $b->product->name ?? '—' }}</span>
            </div>
        </td>
        <td>{{ $b->warehouse->name ?? '—' }}</td>
        <td>
            <span class="users-badge users-badge--role">{{ number_format($b->quantity, 2) }}</span>
        </td>
        <td>{{ $b->product ? $b->product->min_stock_alert : '—' }}</td>
        <td>
            @if ($isLow)
                <span class="users-badge users-badge--inactive">انخفاض</span>
            @else
                <span class="users-badge users-badge--active">طبيعي</span>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد أرصدة — شغّل StockSeeder أو أضف حركات مخزون</td>
    </tr>
@endforelse
