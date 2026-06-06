@forelse($branches as $branch)
    <tr>
        <th scope="row" class="users-row-index">{{ $branches->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-building"></i>
                </div>
                @can('branch-show')
                    <a href="{{ route('admin.branches.show', $branch) }}" class="users-user-name">
                        {{ $branch->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $branch->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($branch->code)
                <span class="users-badge users-badge--role">{{ $branch->code }}</span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($branch->email)
                <div class="users-email-cell">
                    <a href="mailto:{{ $branch->email }}" class="users-email-link" title="إرسال بريد إلكتروني">
                        {{ $branch->email }}
                    </a>
                    <button type="button" class="users-copy-btn" data-copy="{{ $branch->email }}"
                        title="نسخ البريد" aria-label="نسخ البريد">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($branch->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $branch->phone) }}"
                    target="_blank" class="users-phone-cell" title="فتح WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    <span>{{ $branch->phone }}</span>
                </a>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $branch->warehouses_count }}</span>
        </td>
        <td>
            @can('branch-edit')
                <label class="users-toggle">
                    <input type="checkbox"
                        class="users-toggle-input"
                        data-branch-id="{{ $branch->id }}"
                        data-toggle-url="{{ route('admin.branches.toggle-status', $branch) }}"
                        {{ $branch->is_active ? 'checked' : '' }}>
                    <span class="users-toggle-track">
                        <span class="users-toggle-thumb"></span>
                    </span>
                    <span class="users-toggle-label">
                        {{ $branch->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </label>
            @else
                @if ($branch->is_active)
                    <span class="users-badge users-badge--active">نشط</span>
                @else
                    <span class="users-badge users-badge--inactive">غير نشط</span>
                @endif
            @endcan
        </td>
        <td>
            <div class="users-actions">
                @can('branch-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.branches.show', $branch) }}"
                        title="عرض الفرع">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('branch-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.branches.edit', $branch) }}"
                        title="تعديل الفرع">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('branch-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.branches.destroy', $branch) }}"
                        data-delete-title="حذف الفرع"
                        data-delete-message="هل أنت متأكد من حذف هذا الفرع؟"
                        data-delete-item="{{ $branch->name }}"
                        title="حذف الفرع">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد فروع</td>
    </tr>
@endforelse
