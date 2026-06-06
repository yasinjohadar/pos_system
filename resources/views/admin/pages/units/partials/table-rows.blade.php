@forelse ($units as $unit)
    <tr>
        <th scope="row" class="users-row-index">{{ $units->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fas fa-ruler-combined"></i>
                </div>
                @can('unit-show')
                    <a href="{{ route('admin.units.show', $unit) }}" class="users-user-name">
                        {{ $unit->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $unit->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($unit->symbol)
                <span class="users-badge users-badge--role">{{ $unit->symbol }}</span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($unit->baseUnit)
                @can('unit-show')
                    <a href="{{ route('admin.units.show', $unit->baseUnit) }}" class="users-email-link">
                        {{ $unit->baseUnit->name }}
                    </a>
                @else
                    <span>{{ $unit->baseUnit->name }}</span>
                @endcan
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ number_format($unit->conversion_factor, 4) }}</span>
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $unit->products_count }}</span>
        </td>
        <td>
            @can('unit-edit')
                <label class="users-toggle">
                    <input type="checkbox"
                        class="users-toggle-input"
                        data-toggle-url="{{ route('admin.units.toggle-status', $unit) }}"
                        {{ $unit->is_active ? 'checked' : '' }}>
                    <span class="users-toggle-track">
                        <span class="users-toggle-thumb"></span>
                    </span>
                    <span class="users-toggle-label">
                        {{ $unit->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </label>
            @else
                @if ($unit->is_active)
                    <span class="users-badge users-badge--active">نشط</span>
                @else
                    <span class="users-badge users-badge--inactive">غير نشط</span>
                @endif
            @endcan
        </td>
        <td>
            <div class="users-actions">
                @can('unit-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.units.show', $unit) }}"
                        title="عرض الوحدة">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('unit-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.units.edit', $unit) }}"
                        title="تعديل الوحدة">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('unit-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.units.destroy', $unit) }}"
                        data-delete-title="حذف الوحدة"
                        data-delete-message="هل أنت متأكد من حذف هذه الوحدة؟"
                        data-delete-item="{{ $unit->name }}"
                        title="حذف الوحدة">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد وحدات</td>
    </tr>
@endforelse
