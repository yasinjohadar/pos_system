@php
    use App\Models\AppStorageConfig;
@endphp

@forelse ($configs as $config)
    <tr>
        <th scope="row" class="users-row-index">{{ $config->id }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-hdd"></i>
                </div>
                <span class="users-user-name">{{ $config->name }}</span>
            </div>
        </td>
        <td>
            <span class="users-badge users-badge--role">
                {{ AppStorageConfig::DRIVERS[$config->driver] ?? $config->driver }}
            </span>
        </td>
        <td>
            @if ($config->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $config->priority }}</span>
        </td>
        <td>
            <div class="users-actions">
                <a href="{{ route('admin.storage.edit', $config->id) }}"
                    class="users-action-btn users-action-btn--edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <button type="button" class="users-action-btn users-action-btn--view test-storage-btn"
                    data-test-url="{{ route('admin.storage.test', $config->id) }}"
                    title="اختبار الاتصال">
                    <i class="fas fa-vial"></i>
                </button>
                <button type="button" class="users-action-btn users-action-btn--delete"
                    data-bs-toggle="modal"
                    data-bs-target="#deleteConfirmModal"
                    data-delete-action="{{ route('admin.storage.destroy', $config->id) }}"
                    data-delete-title="حذف مكان التخزين"
                    data-delete-message="هل أنت متأكد من حذف هذه الإعدادات؟"
                    data-delete-item="{{ $config->name }}"
                    title="حذف">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد إعدادات تخزين.</td>
    </tr>
@endforelse
