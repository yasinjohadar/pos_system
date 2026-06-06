@forelse ($segments as $segment)
    <tr>
        <th scope="row" class="users-row-index">{{ $segments->firstItem() + $loop->index }}</th>
        <td>{{ $segment->name }}</td>
        <td>{{ \Illuminate\Support\Str::limit($segment->description, 50) ?: '—' }}</td>
        <td>
            <span class="users-badge" style="background-color: {{ $segment->color }}; color: #fff;">{{ $segment->color }}</span>
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $segment->customers_count }}</span>
        </td>
        <td>
            @if ($segment->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('customer-segment-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.customer-segments.edit', $segment) }}"
                        title="تعديل الشريحة">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('customer-segment-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.customer-segments.destroy', $segment) }}"
                        data-delete-title="حذف الشريحة"
                        data-delete-message="هل أنت متأكد من حذف هذه الشريحة؟"
                        data-delete-item="{{ $segment->name }}"
                        title="حذف الشريحة">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد شرائح عملاء حالياً</td>
    </tr>
@endforelse
