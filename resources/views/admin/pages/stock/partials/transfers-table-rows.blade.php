@forelse ($transfers as $t)
    <tr>
        <th scope="row" class="users-row-index">{{ $transfers->firstItem() + $loop->index }}</th>
        <td>{{ $t->transfer_date->format('Y-m-d') }}</td>
        <td>{{ $t->fromWarehouse->name ?? '—' }}</td>
        <td>{{ $t->toWarehouse->name ?? '—' }}</td>
        <td>
            @if ($t->status === 'completed')
                <span class="users-badge users-badge--active">مكتمل</span>
            @elseif ($t->status === 'pending')
                <span class="users-badge users-badge--role">قيد الانتظار</span>
            @else
                <span class="users-badge users-badge--inactive">{{ $t->status }}</span>
            @endif
        </td>
        <td>{{ $t->user->name ?? '—' }}</td>
        <td>
            <div class="users-actions">
                <a class="users-action-btn users-action-btn--view"
                    href="{{ route('admin.stock.transfers.show', $t) }}"
                    title="عرض التحويل">
                    <i class="fa-solid fa-eye"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد تحويلات مخزون</td>
    </tr>
@endforelse
