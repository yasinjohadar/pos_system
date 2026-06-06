@php
    use App\Models\BackupSchedule;
@endphp

@forelse ($schedules as $schedule)
    <tr>
        <th scope="row" class="users-row-index">{{ $schedules->firstItem() ? $schedules->firstItem() + $loop->index : $schedule->id }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-clock"></i>
                </div>
                <span class="users-user-name">{{ $schedule->name }}</span>
            </div>
        </td>
        <td>
            <span class="users-badge users-badge--role">
                {{ BackupSchedule::BACKUP_TYPES[$schedule->backup_type] ?? $schedule->backup_type }}
            </span>
        </td>
        <td>{{ BackupSchedule::FREQUENCIES[$schedule->frequency] ?? $schedule->frequency }}</td>
        <td class="users-muted-text" dir="ltr">{{ $schedule->time }}</td>
        <td>
            @if ($schedule->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td class="users-muted-text">{{ $schedule->next_run_at?->format('Y-m-d H:i') ?? '—' }}</td>
        <td>
            <div class="users-actions">
                <a href="{{ route('admin.backup-schedules.edit', $schedule->id) }}"
                    class="users-action-btn users-action-btn--edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.backup-schedules.execute', $schedule->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="users-action-btn users-action-btn--view" title="تشغيل الآن">
                        <i class="fas fa-play"></i>
                    </button>
                </form>
                <form action="{{ route('admin.backup-schedules.toggle-active', $schedule->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"
                        class="users-action-btn {{ $schedule->is_active ? 'users-action-btn--delete' : 'users-action-btn--edit' }}"
                        title="{{ $schedule->is_active ? 'تعطيل' : 'تفعيل' }}">
                        <i class="fas fa-{{ $schedule->is_active ? 'ban' : 'check' }}"></i>
                    </button>
                </form>
                <button type="button" class="users-action-btn users-action-btn--delete"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmModal"
                    data-delete-action="{{ route('admin.backup-schedules.destroy', $schedule->id) }}"
                    data-delete-title="حذف الجدولة"
                    data-delete-message="هل أنت متأكد من حذف هذه الجدولة؟"
                    data-delete-item="{{ $schedule->name }}"
                    title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد جدولات.</td>
    </tr>
@endforelse
