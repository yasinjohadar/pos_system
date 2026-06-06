@php
    use App\Models\Backup;
@endphp

@forelse ($backups as $backup)
    @php
        $backupDeleteDetails = 'اسم النسخة: ' . $backup->name
            . ' | النوع: ' . (Backup::BACKUP_TYPES[$backup->backup_type] ?? $backup->backup_type)
            . ' | الحجم: ' . $backup->getFileSize()
            . ' | التاريخ: ' . $backup->created_at->format('Y-m-d H:i');
    @endphp
    <tr>
        <th scope="row" class="users-row-index">{{ $backups->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-file-archive"></i>
                </div>
                <a href="{{ route('admin.backups.show', $backup->id) }}" class="users-user-name">
                    {{ $backup->name }}
                </a>
            </div>
        </td>
        <td>
            <span class="users-badge users-badge--role">
                {{ Backup::BACKUP_TYPES[$backup->backup_type] ?? $backup->backup_type }}
            </span>
        </td>
        <td>
            @if ($backup->status === 'completed')
                <span class="users-badge users-badge--active">مكتمل</span>
            @elseif ($backup->status === 'failed')
                <span class="users-badge users-badge--inactive">فشل</span>
            @elseif ($backup->status === 'running')
                <span class="users-badge backup-badge--running">قيد التنفيذ</span>
            @else
                <span class="users-badge users-badge--role">معلق</span>
            @endif
        </td>
        <td>{{ $backup->getFileSize() }}</td>
        <td class="users-muted-text">{{ $backup->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <div class="users-actions">
                <a href="{{ route('admin.backups.show', $backup->id) }}"
                    class="users-action-btn users-action-btn--view" title="عرض">
                    <i class="fas fa-eye"></i>
                </a>
                @if ($backup->status === 'completed')
                    <a href="{{ route('admin.backups.download', $backup->id) }}"
                        class="users-action-btn users-action-btn--edit" title="تحميل">
                        <i class="fas fa-download"></i>
                    </a>
                @endif
                <button type="button" class="users-action-btn users-action-btn--delete"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmModal"
                    data-delete-action="{{ route('admin.backups.destroy', $backup->id) }}"
                    data-delete-title="حذف النسخة الاحتياطية"
                    data-delete-message="هل أنت متأكد من حذف هذه النسخة الاحتياطية؟"
                    data-delete-item="{{ $backup->name }}"
                    data-delete-details="{{ $backupDeleteDetails }}"
                    title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد نسخ احتياطية.</td>
    </tr>
@endforelse
