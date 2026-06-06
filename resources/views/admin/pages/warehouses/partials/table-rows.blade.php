@forelse ($warehouses as $warehouse)
    <tr>
        <th scope="row" class="users-row-index">{{ $warehouses->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                @can('warehouse-show')
                    <a href="{{ route('admin.warehouses.show', $warehouse) }}" class="users-user-name">
                        {{ $warehouse->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $warehouse->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($warehouse->code)
                <span class="users-badge users-badge--role">{{ $warehouse->code }}</span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($warehouse->branch)
                @can('branch-show')
                    <a href="{{ route('admin.branches.show', $warehouse->branch) }}" class="users-email-link">
                        {{ $warehouse->branch->name }}
                    </a>
                @else
                    <span>{{ $warehouse->branch->name }}</span>
                @endcan
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($warehouse->is_default)
                <span class="users-badge users-badge--role">
                    <i class="fas fa-star" style="font-size: 0.625rem;"></i>
                    افتراضي
                </span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @can('warehouse-edit')
                <label class="users-toggle">
                    <input type="checkbox"
                        class="users-toggle-input"
                        data-warehouse-id="{{ $warehouse->id }}"
                        data-toggle-url="{{ route('admin.warehouses.toggle-status', $warehouse) }}"
                        {{ $warehouse->is_active ? 'checked' : '' }}>
                    <span class="users-toggle-track">
                        <span class="users-toggle-thumb"></span>
                    </span>
                    <span class="users-toggle-label">
                        {{ $warehouse->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </label>
            @else
                @if ($warehouse->is_active)
                    <span class="users-badge users-badge--active">نشط</span>
                @else
                    <span class="users-badge users-badge--inactive">غير نشط</span>
                @endif
            @endcan
        </td>
        <td>
            <div class="users-actions">
                @can('warehouse-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.warehouses.show', $warehouse) }}"
                        title="عرض المخزن">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('warehouse-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.warehouses.edit', $warehouse) }}"
                        title="تعديل المخزن">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('warehouse-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.warehouses.destroy', $warehouse) }}"
                        data-delete-title="حذف المخزن"
                        data-delete-message="هل أنت متأكد من حذف هذا المخزن؟"
                        data-delete-item="{{ $warehouse->name }}"
                        title="حذف المخزن">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد مخازن</td>
    </tr>
@endforelse
