@forelse ($years as $y)
    <tr>
        <th scope="row" class="users-row-index">{{ $years->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-user-name" style="cursor: default;">{{ $y->name }}</span>
        </td>
        <td>{{ $y->start_date->format('Y-m-d') }}</td>
        <td>{{ $y->end_date->format('Y-m-d') }}</td>
        <td>
            @if ($y->is_active)
                <span class="users-badge users-badge--active">نعم</span>
            @else
                <span class="users-badge users-badge--inactive">لا</span>
            @endif
        </td>
        <td>
            @if ($y->is_closed)
                <span class="users-badge users-badge--inactive">نعم</span>
            @else
                <span class="users-badge users-badge--active">لا</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @if (! $y->is_closed)
                    <form action="{{ route('admin.fiscal-years.close', $y) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('إقفال هذه السنة المالية؟');">
                        @csrf
                        <button type="submit" class="users-action-btn users-action-btn--delete" title="إقفال السنة">
                            <i class="fas fa-lock"></i>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد سنوات مالية</td>
    </tr>
@endforelse
