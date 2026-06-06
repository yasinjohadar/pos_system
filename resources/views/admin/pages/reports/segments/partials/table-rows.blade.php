@forelse ($rows as $row)
    <tr>
        <td>
            <div class="d-flex align-items-center gap-2">
                @if (! empty($row->segment_color))
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $row->segment_color }}; flex-shrink: 0;"></span>
                @endif
                <span class="users-user-name" style="cursor: default;">{{ $row->segment_name }}</span>
            </div>
        </td>
        <td><span class="users-badge users-badge--role">{{ $row->customer_count }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($row->total_sales, 2) }}</span></td>
        <td><span class="users-amount">{{ number_format($row->avg_balance, 2) }}</span></td>
        <td><span class="users-badge users-badge--role">{{ $row->invoice_count }}</span></td>
        <td><span class="users-amount">{{ number_format($row->avg_invoice_value, 2) }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد شرائح نشطة — شغّل <code>SalesSeeder</code> أو أضف شرائح من إدارة الشرائح</td>
    </tr>
@endforelse

@if (count($rows) > 0)
    @php
        $totalCustomers = collect($rows)->sum('customer_count');
        $totalSales = collect($rows)->sum('total_sales');
        $totalInvoices = collect($rows)->sum('invoice_count');
    @endphp
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td class="text-end" style="padding: 0.875rem 1rem;">الإجمالي:</td>
        <td><span class="users-badge users-badge--role">{{ $totalCustomers }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($totalSales, 2) }}</span></td>
        <td colspan="2"><span class="users-badge users-badge--role">{{ $totalInvoices }}</span> فاتورة</td>
        <td></td>
    </tr>
@endif
