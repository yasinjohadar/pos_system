@forelse ($orders as $order)
    <tr>
        <th scope="row" class="users-row-index">{{ $orders->firstItem() + $loop->index }}</th>
        <td><span class="users-badge users-badge--role">{{ $order->number }}</span></td>
        <td>{{ $order->order_date->format('Y-m-d') }}</td>
        <td>{{ $order->supplier->name ?? '—' }}</td>
        <td>{{ $order->branch->name ?? '—' }}</td>
        <td><span class="users-amount">{{ number_format($order->total, 2) }}</span></td>
        <td>
            @php
                $statusLabels = ['draft' => 'مسودة', 'sent' => 'مُرسل', 'received' => 'مُستلم', 'converted' => 'محوّل', 'cancelled' => 'ملغى'];
            @endphp
            <span class="users-badge users-badge--role">{{ $statusLabels[$order->status] ?? $order->status }}</span>
        </td>
        <td>
            <div class="users-actions">
                @can('purchase-order-show')
                    <a class="users-action-btn users-action-btn--view" href="{{ route('admin.purchase-orders.show', $order) }}" title="عرض">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr><td colspan="8" class="users-empty">لا توجد أوامر شراء</td></tr>
@endforelse
