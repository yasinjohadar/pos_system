@forelse ($mappings as $mapping)
    <tr>
        <th scope="row" class="users-row-index">{{ $mapping->id }}</th>
        <td>
            <span class="users-color-value">{{ $mapping->disk_name }}</span>
        </td>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-link"></i>
                </div>
                <span class="users-user-name">{{ $mapping->label }}</span>
            </div>
        </td>
        <td>
            <span class="users-badge users-badge--role">
                {{ $mapping->primaryStorage->name ?? '—' }}
            </span>
        </td>
        <td>
            @if ($mapping->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                <a href="{{ route('admin.storage-disk-mappings.edit', $mapping->id) }}"
                    class="users-action-btn users-action-btn--edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="users-action-btn users-action-btn--delete"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmModal"
                    data-delete-action="{{ route('admin.storage-disk-mappings.destroy', $mapping->id) }}"
                    data-delete-title="حذف ربط القرص"
                    data-delete-message="هل أنت متأكد من حذف هذا الربط؟"
                    data-delete-item="{{ $mapping->label }}"
                    data-delete-details="Disk: {{ $mapping->disk_name }}"
                    title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد عمليات ربط.</td>
    </tr>
@endforelse
