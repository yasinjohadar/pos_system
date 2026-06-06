@forelse ($attachments as $a)
    <tr>
        <th scope="row" class="users-row-index">{{ $attachments->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-paperclip"></i></div>
                <div>
                    <a href="{{ asset('storage/' . $a->path) }}" target="_blank" class="users-user-name">
                        {{ $a->original_filename }}
                    </a>
                    <small class="users-muted-text d-block">{{ number_format($a->size / 1024, 1) }} KB</small>
                </div>
            </div>
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $attachableTypes[$a->attachable_type] ?? class_basename($a->attachable_type) }}</span>
        </td>
        <td><span class="users-badge users-badge--role">#{{ $a->attachable_id }}</span></td>
        <td>{{ $types[$a->type] ?? $a->type }}</td>
        <td>{{ $a->uploadedBy->name ?? '—' }}</td>
        <td>{{ $a->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <div class="users-actions">
                <a class="users-action-btn users-action-btn--view"
                    href="{{ asset('storage/' . $a->path) }}"
                    target="_blank"
                    title="فتح الملف">
                    <i class="fa-solid fa-external-link-alt"></i>
                </a>
                @can('attachment-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.attachments.destroy', $a) }}"
                        data-delete-title="حذف المرفق"
                        data-delete-message="هل أنت متأكد من حذف هذا المرفق؟"
                        data-delete-item="{{ $a->original_filename }}"
                        title="حذف">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد مرفقات</td>
    </tr>
@endforelse
