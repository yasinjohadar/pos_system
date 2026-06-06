@forelse ($rows as $row)
    <tr>
        <td>
            <span class="users-user-name" style="cursor: default;">{{ $row->product_name }}</span>
        </td>
        <td>
            @if ($row->category_name && $row->category_name !== '—')
                <span class="users-badge users-badge--role">{{ $row->category_name }}</span>
            @else
                —
            @endif
        </td>
        <td><span class="users-amount">{{ number_format($row->total_qty, 2) }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($row->total_revenue, 2) }}</span></td>
        <td><span class="users-amount {{ $row->profit >= 0 ? 'users-qty--in' : 'users-qty--out' }}">{{ number_format($row->profit, 2) }}</span></td>
        <td>
            <span class="users-badge users-badge--role">{{ $row->margin_percent }}%</span>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد بيانات في الفترة المحددة — شغّل <code>SalesSeeder</code></td>
    </tr>
@endforelse

@if (count($rows) > 0)
    @php
        $totalQty = $rows->sum('total_qty');
        $totalRevenue = $rows->sum('total_revenue');
        $totalProfit = $rows->sum('profit');
    @endphp
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td colspan="2" class="text-end" style="padding: 0.875rem 1rem;">المجموع ({{ count($rows) }} منتج):</td>
        <td><span class="users-amount">{{ number_format($totalQty, 2) }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($totalRevenue, 2) }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($totalProfit, 2) }}</span></td>
        <td></td>
    </tr>
@endif
