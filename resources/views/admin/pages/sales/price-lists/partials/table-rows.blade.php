@forelse ($priceLists as $list)
    <tr>
        <th scope="row" class="users-row-index">{{ $priceLists->firstItem() + $loop->index }}</th>
        <td>{{ $list->name }}</td>
        <td>{{ \Illuminate\Support\Str::limit($list->description, 60) ?: '—' }}</td>
        <td>
            @if ($list->is_active)
                <span class="users-badge users-badge--active">نشطة</span>
            @else
                <span class="users-badge users-badge--inactive">متوقفة</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $list->items_count }}</span>
        </td>
        <td>
            <div class="users-actions">
                @can('price-list-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.price-lists.edit', $list) }}"
                        title="تعديل القائمة">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('price-list-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.price-lists.destroy', $list) }}"
                        data-delete-title="حذف قائمة الأسعار"
                        data-delete-message="هل أنت متأكد من حذف هذه القائمة؟"
                        data-delete-item="{{ $list->name }}"
                        title="حذف القائمة">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد قوائم أسعار حالياً</td>
    </tr>
@endforelse
