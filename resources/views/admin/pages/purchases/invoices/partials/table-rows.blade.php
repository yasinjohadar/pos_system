@forelse ($invoices as $inv)
    <tr>
        <th scope="row" class="users-row-index">{{ $invoices->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role">{{ $inv->number }}</span>
        </td>
        <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
        <td>{{ $inv->branch->name ?? '—' }}</td>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-truck"></i></div>
                <span class="users-user-name" style="cursor: default;">{{ $inv->supplier->name ?? '—' }}</span>
            </div>
        </td>
        <td>
            <span class="users-amount">{{ number_format($inv->total, 2) }}</span>
        </td>
        <td>
            @if ($inv->payment_status === 'paid')
                <span class="users-badge users-badge--active">مدفوع</span>
            @elseif ($inv->payment_status === 'partial')
                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">جزئي</span>
            @else
                <span class="users-badge users-badge--inactive">معلق</span>
            @endif
        </td>
        <td>
            @if ($inv->status === 'draft')
                <span class="users-badge users-badge--role">مسودة</span>
            @elseif ($inv->status === 'confirmed')
                <span class="users-badge users-badge--active">معتمدة</span>
            @else
                <span class="users-badge users-badge--inactive">ملغاة</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('purchase-invoice-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.purchase-invoices.show', $inv) }}"
                        title="عرض الفاتورة">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('purchase-invoice-edit')
                    @if ($inv->status === 'draft')
                        <a class="users-action-btn users-action-btn--edit"
                            href="{{ route('admin.purchase-invoices.edit', $inv) }}"
                            title="تعديل الفاتورة">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    @endif
                @endcan
                @can('purchase-invoice-delete')
                    @if ($inv->status === 'draft')
                        <button type="button" class="users-action-btn users-action-btn--delete"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteConfirmModal"
                            data-delete-action="{{ route('admin.purchase-invoices.destroy', $inv) }}"
                            data-delete-title="حذف الفاتورة"
                            data-delete-message="هل أنت متأكد من حذف هذه الفاتورة؟"
                            data-delete-item="{{ $inv->number }}"
                            title="حذف الفاتورة">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    @endif
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="users-empty">لا توجد فواتير شراء — شغّل PurchaseSeeder أو أنشئ فاتورة جديدة</td>
    </tr>
@endforelse
