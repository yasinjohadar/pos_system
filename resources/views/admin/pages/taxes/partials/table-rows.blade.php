@forelse ($taxes as $tax)
    <tr>
        <th scope="row" class="users-row-index">{{ $taxes->firstItem() + $loop->index }}</th>
        <td>{{ $tax->name }}</td>
        <td>
            @if ($tax->type === 'percent')
                <span class="users-badge users-badge--role">نسبة مئوية</span>
            @else
                <span class="users-badge users-badge--role">مبلغ ثابت</span>
            @endif
        </td>
        <td>
            @if ($tax->type === 'percent')
                {{ number_format($tax->rate, 2) }}%
            @else
                {{ number_format($tax->rate, 2) }}
            @endif
        </td>
        <td>
            @if ($tax->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('tax-edit')
                    <a class="users-action-btn users-action-btn--edit" href="{{ route('admin.taxes.edit', $tax) }}" title="تعديل">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('tax-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal" data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.taxes.destroy', $tax) }}"
                        data-delete-title="حذف الضريبة"
                        data-delete-message="هل أنت متأكد من حذف هذه الضريبة؟"
                        data-delete-item="{{ $tax->name }}" title="حذف">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد ضرائب</td>
    </tr>
@endforelse
