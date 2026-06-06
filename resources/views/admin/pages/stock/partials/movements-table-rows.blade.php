@forelse ($movements as $m)
    <tr>
        <th scope="row" class="users-row-index">{{ $movements->firstItem() + $loop->index }}</th>
        <td>{{ $m->movement_date->format('Y-m-d') }}</td>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-box"></i></div>
                <span class="users-user-name" style="cursor: default;">{{ $m->product->name ?? '—' }}</span>
            </div>
        </td>
        <td>{{ $m->warehouse->name ?? '—' }}</td>
        <td>
            @php
                $typeClass = in_array($m->type, ['in', 'transfer_in', 'return_sale']) ? 'users-badge--active'
                    : (in_array($m->type, ['out', 'transfer_out', 'return_purchase']) ? 'users-badge--inactive' : 'users-badge--role');
            @endphp
            <span class="users-badge {{ $typeClass }}">
                {{ \App\Models\StockMovement::TYPE_LABELS[$m->type] ?? $m->type }}
            </span>
        </td>
        <td>
            <span class="users-badge users-badge--role {{ $m->quantity >= 0 ? 'users-qty--in' : 'users-qty--out' }}">
                {{ $m->quantity >= 0 ? '+' : '' }}{{ number_format($m->quantity, 2) }}
            </span>
        </td>
        <td>{{ $m->user->name ?? '—' }}</td>
        <td><span class="users-muted-text">{{ Str::limit($m->notes, 40) ?: '—' }}</span></td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد حركات مخزون</td>
    </tr>
@endforelse
