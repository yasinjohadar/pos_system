@php
    $typeLabels = [
        'asset' => 'أصول',
        'liability' => 'خصوم',
        'equity' => 'حقوق ملكية',
        'revenue' => 'إيرادات',
        'expense' => 'مصروفات',
    ];
@endphp

@forelse ($accounts as $a)
    <tr>
        <th scope="row" class="users-row-index">{{ $accounts->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role" dir="ltr">{{ $a->code }}</span>
        </td>
        <td>
            <span class="users-user-name" style="cursor: default;">{{ $a->name }}</span>
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $typeLabels[$a->type] ?? $a->type }}</span>
        </td>
        <td>
            @if ($a->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $a->journal_entry_lines_count }}</span>
        </td>
        <td>
            <div class="users-actions">
                @can('chart-of-account-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.chart-of-accounts.edit', $a) }}"
                        title="تعديل">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('chart-of-account-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.chart-of-accounts.destroy', $a) }}"
                        data-delete-title="حذف الحساب"
                        data-delete-message="هل أنت متأكد من حذف هذا الحساب؟"
                        data-delete-item="{{ $a->code }} — {{ $a->name }}"
                        title="حذف">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد حسابات</td>
    </tr>
@endforelse
