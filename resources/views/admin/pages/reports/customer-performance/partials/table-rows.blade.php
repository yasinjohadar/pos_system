@forelse ($rows as $row)
    <tr>
        <td><span class="users-user-name" style="cursor: default;">{{ $row->customer_name }}</span></td>
        <td><span class="users-badge users-badge--role">{{ $row->invoice_count }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($row->total_sales, 2) }}</span></td>
        <td><span class="users-amount">{{ number_format($row->avg_invoice_value, 2) }}</span></td>
        <td>
            @if ($row->last_invoice_date)
                <span class="users-muted-text">{{ \Carbon\Carbon::parse($row->last_invoice_date)->format('Y-m-d') }}</span>
            @else
                —
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="users-empty">لا توجد بيانات في الفترة المحددة — شغّل <code>SalesSeeder</code></td>
    </tr>
@endforelse

@if (count($rows) > 0)
    @php
        $totalInvoices = $rows->sum('invoice_count');
        $totalSales = $rows->sum('total_sales');
    @endphp
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td class="text-end" style="padding: 0.875rem 1rem;">المجموع ({{ count($rows) }} عميل):</td>
        <td><span class="users-badge users-badge--role">{{ $totalInvoices }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($totalSales, 2) }}</span></td>
        <td colspan="2"></td>
    </tr>
@endif
