@forelse ($suppliers as $supplier)
    <tr>
        <th scope="row" class="users-row-index">{{ $suppliers->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-truck"></i></div>
                @can('supplier-show')
                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="users-user-name">
                        {{ $supplier->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $supplier->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($supplier->phone)
                <a href="tel:{{ $supplier->phone }}" class="users-phone-cell" title="اتصال">
                    <i class="fas fa-phone"></i>
                    <span>{{ $supplier->phone }}</span>
                </a>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($supplier->email)
                <div class="users-email-cell">
                    <a href="mailto:{{ $supplier->email }}" class="users-email-link">{{ $supplier->email }}</a>
                    <button type="button" class="users-copy-btn" data-copy="{{ $supplier->email }}" title="نسخ البريد">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($supplier->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('supplier-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.suppliers.show', $supplier) }}"
                        title="عرض المورد">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('supplier-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.suppliers.edit', $supplier) }}"
                        title="تعديل المورد">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('supplier-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.suppliers.destroy', $supplier) }}"
                        data-delete-title="حذف المورد"
                        data-delete-message="هل أنت متأكد من حذف هذا المورد؟"
                        data-delete-item="{{ $supplier->name }}"
                        title="حذف المورد">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا يوجد موردون — شغّل PurchaseSeeder أو أضف مورداً جديداً</td>
    </tr>
@endforelse
