@forelse ($paymentMethods as $pm)
    <tr>
        <th scope="row" class="users-row-index">{{ $paymentMethods->firstItem() + $loop->index }}</th>
        <td>{{ $pm->name }}</td>
        <td>
            <span class="users-badge users-badge--role">{{ $pm->code }}</span>
        </td>
        <td>{{ $pm->sort_order }}</td>
        <td>
            @if ($pm->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('payment-method-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.payment-methods.edit', $pm) }}"
                        title="تعديل طريقة الدفع">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('payment-method-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.payment-methods.destroy', $pm) }}"
                        data-delete-title="حذف طريقة الدفع"
                        data-delete-message="هل أنت متأكد من حذف طريقة الدفع؟"
                        data-delete-item="{{ $pm->name }}"
                        title="حذف طريقة الدفع">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد طرق دفع</td>
    </tr>
@endforelse
