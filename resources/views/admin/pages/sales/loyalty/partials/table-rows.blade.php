@forelse ($transactions as $tx)
    <tr>
        <th scope="row" class="users-row-index">{{ $transactions->firstItem() + $loop->index }}</th>
        <td>{{ $tx->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-user"></i></div>
                <span class="users-user-name" style="cursor: default;">{{ $tx->customer->name ?? '—' }}</span>
            </div>
        </td>
        <td>
            @switch($tx->type)
                @case('earn')
                    <span class="users-badge users-badge--active">اكتساب</span>
                    @break
                @case('redeem')
                    <span class="users-badge users-badge--role" style="background: rgba(59, 130, 246, 0.15); color: #2563eb;">استبدال</span>
                    @break
                @case('adjustment')
                    <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">تعديل</span>
                    @break
                @case('expire')
                    <span class="users-badge users-badge--inactive">انتهاء</span>
                    @break
                @default
                    {{ $tx->type }}
            @endswitch
        </td>
        <td>
            @if ($tx->points > 0)
                <span class="users-amount" style="color: #059669;">+{{ $tx->points }}</span>
            @else
                <span class="users-amount" style="color: #dc2626;">{{ $tx->points }}</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $tx->balance_after }}</span>
        </td>
        <td>{{ \Illuminate\Support\Str::limit($tx->description, 40) ?: '—' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد حركات نقاط</td>
    </tr>
@endforelse
