@forelse ($returns as $ret)
    <tr>
        <th scope="row" class="users-row-index">{{ $returns->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role">{{ $ret->return_number }}</span>
        </td>
        <td>
            <a href="{{ route('admin.sale-invoices.show', $ret->sale_invoice_id) }}" class="users-user-name">
                {{ $ret->saleInvoice->number ?? $ret->sale_invoice_id }}
            </a>
        </td>
        <td>{{ $ret->return_date->format('Y-m-d') }}</td>
        <td>{{ $ret->warehouse->name ?? '—' }}</td>
        <td>
            <span class="users-amount">{{ number_format($ret->total_refund, 2) }}</span>
        </td>
        <td>
            @if ($ret->status === 'pending')
                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">قيد الانتظار</span>
            @elseif ($ret->status === 'completed')
                <span class="users-badge users-badge--active">مكتمل</span>
            @else
                <span class="users-badge users-badge--inactive">ملغى</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('sale-return-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.sale-returns.show', $ret) }}"
                        title="عرض المرتجع">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد مرتجعات</td>
    </tr>
@endforelse
