@forelse ($rows as $i => $row)
    <tr>
        <td><span class="users-row-index">{{ $i + 1 }}</span></td>
        <td><span class="users-user-name" style="cursor: default;">{{ $row->customer_name }}</span></td>
        <td><span class="users-badge users-badge--role">{{ $row->invoice_count }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($row->total_sales, 2) }}</span></td>
        <td><span class="users-amount">{{ number_format($row->avg_invoice_value, 2) }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="users-empty">لا توجد بيانات — شغّل <code>SalesSeeder</code></td>
    </tr>
@endforelse
