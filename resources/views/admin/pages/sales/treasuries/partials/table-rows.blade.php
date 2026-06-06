@forelse ($treasuries as $t)
    <tr>
        <th scope="row" class="users-row-index">{{ $treasuries->firstItem() + $loop->index }}</th>
        <td>{{ $t->name }}</td>
        <td>
            <span class="users-badge users-badge--role">{{ $t->type === 'cashbox' ? 'خزنة' : 'بنك' }}</span>
        </td>
        <td>{{ $t->branch->name ?? '—' }}</td>
        <td>
            <span class="users-amount">{{ number_format((float) ($t->opening_balance ?? 0), 2) }}</span>
        </td>
        <td>{{ $t->currency ?? '—' }}</td>
        <td>
            @if ($t->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('treasury-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.treasuries.edit', $t) }}"
                        title="تعديل">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('treasury-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.treasuries.destroy', $t) }}"
                        data-delete-title="حذف الخزنة / البنك"
                        data-delete-message="هل أنت متأكد من الحذف؟"
                        data-delete-item="{{ $t->name }}"
                        title="حذف">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد خزائن أو بنوك</td>
    </tr>
@endforelse
