@forelse ($categories as $category)
    <tr>
        <th scope="row" class="users-row-index">{{ $categories->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    @if ($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}">
                    @else
                        <i class="fas fa-folder"></i>
                    @endif
                </div>
                @can('category-show')
                    <a href="{{ route('admin.categories.show', $category) }}" class="users-user-name">
                        {{ $category->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $category->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($category->parent)
                @can('category-show')
                    <a href="{{ route('admin.categories.show', $category->parent) }}" class="users-email-link">
                        {{ $category->parent->name }}
                    </a>
                @else
                    <span>{{ $category->parent->name }}</span>
                @endcan
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $category->products_count }}</span>
        </td>
        <td>
            @can('category-edit')
                <label class="users-toggle">
                    <input type="checkbox"
                        class="users-toggle-input"
                        data-category-id="{{ $category->id }}"
                        data-toggle-url="{{ route('admin.categories.toggle-status', $category) }}"
                        {{ $category->is_active ? 'checked' : '' }}>
                    <span class="users-toggle-track">
                        <span class="users-toggle-thumb"></span>
                    </span>
                    <span class="users-toggle-label">
                        {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                    </span>
                </label>
            @else
                @if ($category->is_active)
                    <span class="users-badge users-badge--active">نشط</span>
                @else
                    <span class="users-badge users-badge--inactive">غير نشط</span>
                @endif
            @endcan
        </td>
        <td>
            <div class="users-actions">
                @can('category-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.categories.show', $category) }}"
                        title="عرض التصنيف">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('category-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.categories.edit', $category) }}"
                        title="تعديل التصنيف">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('category-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.categories.destroy', $category) }}"
                        data-delete-title="حذف التصنيف"
                        data-delete-message="هل أنت متأكد من حذف هذا التصنيف؟"
                        data-delete-item="{{ $category->name }}"
                        title="حذف التصنيف">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد تصنيفات</td>
    </tr>
@endforelse
