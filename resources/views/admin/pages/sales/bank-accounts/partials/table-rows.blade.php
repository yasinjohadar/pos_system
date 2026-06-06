@forelse ($bankAccounts as $ba)
    <tr>
        <th scope="row" class="users-row-index">{{ $bankAccounts->firstItem() + $loop->index }}</th>
        <td>{{ $ba->name }}</td>
        <td>
            <span class="users-badge users-badge--role">{{ $ba->account_number ?? '—' }}</span>
        </td>
        <td>{{ $ba->branch->name ?? '—' }}</td>
        <td>
            <span class="users-amount">{{ number_format((float) ($ba->opening_balance ?? 0), 2) }}</span>
        </td>
        <td>{{ $ba->currency ?? '—' }}</td>
        <td>
            @if ($ba->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('bank-account-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.bank-accounts.edit', $ba) }}"
                        title="تعديل الحساب">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('bank-account-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.bank-accounts.destroy', $ba) }}"
                        data-delete-title="حذف الحساب البنكي"
                        data-delete-message="هل أنت متأكد من الحذف؟"
                        data-delete-item="{{ $ba->name }}"
                        title="حذف الحساب">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد حسابات بنكية</td>
    </tr>
@endforelse
