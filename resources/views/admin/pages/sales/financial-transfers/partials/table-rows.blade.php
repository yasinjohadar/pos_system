@forelse ($transfers as $t)
    <tr>
        <th scope="row" class="users-row-index">{{ $transfers->firstItem() + $loop->index }}</th>
        <td>{{ $t->transfer_date->format('Y-m-d') }}</td>
        <td>{{ $t->from_source_name }}</td>
        <td>{{ $t->to_target_name }}</td>
        <td>
            <span class="users-amount">{{ number_format($t->amount, 2) }}</span>
        </td>
        <td>{{ $t->reference ?? '—' }}</td>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-user"></i></div>
                <span class="users-user-name" style="cursor: default;">{{ $t->user->name ?? '—' }}</span>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد تحويلات</td>
    </tr>
@endforelse
