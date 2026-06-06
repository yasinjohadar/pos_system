@forelse ($rows as $c)
    <tr>
        <td><span class="users-user-name" style="cursor: default;">{{ $c->name }}</span></td>
        <td>
            @if ($c->phone)
                <span class="users-badge users-badge--role" dir="ltr">{{ $c->phone }}</span>
            @else
                —
            @endif
        </td>
        <td>
            @if ($c->email)
                <span class="users-muted-text" dir="ltr">{{ $c->email }}</span>
            @else
                —
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="3" class="users-empty">لا يوجد عملاء غير نشطين في هذه الفترة</td>
    </tr>
@endforelse

@if (count($rows) > 0)
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td colspan="2" class="text-end" style="padding: 0.875rem 1rem;">عدد العملاء:</td>
        <td><span class="users-amount">{{ count($rows) }}</span></td>
    </tr>
@endif
