@forelse ($rows as $i => $row)
    <tr>
        <td><span class="users-row-index">{{ $i + 1 }}</span></td>
        <td>
            <span class="users-user-name" style="cursor: default;">{{ $row->product_name }}</span>
            @if ($row->category_name && $row->category_name !== '—')
                <small class="users-muted-text d-block">{{ $row->category_name }}</small>
            @endif
        </td>
        <td><span class="users-amount">{{ number_format($row->total_qty, 2) }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($row->total_revenue, 2) }}</span></td>
        <td><span class="users-amount {{ $row->profit >= 0 ? 'users-qty--in' : 'users-qty--out' }}">{{ number_format($row->profit, 2) }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="users-empty">لا توجد بيانات — شغّل <code>SalesSeeder</code></td>
    </tr>
@endforelse
