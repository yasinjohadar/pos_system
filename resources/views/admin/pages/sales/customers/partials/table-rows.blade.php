@forelse ($customers as $customer)
    <tr>
        <th scope="row" class="users-row-index">{{ $customers->firstItem() + $loop->index }}</th>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar"><i class="fas fa-user-tie"></i></div>
                @can('customer-show')
                    <a href="{{ route('admin.customers.show', $customer) }}" class="users-user-name">
                        {{ $customer->name }}
                    </a>
                @else
                    <span class="users-user-name">{{ $customer->name }}</span>
                @endcan
            </div>
        </td>
        <td>
            @if ($customer->phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->phone) }}"
                    target="_blank" class="users-phone-cell" title="فتح WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    <span>{{ $customer->phone }}</span>
                </a>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($customer->email)
                <div class="users-email-cell">
                    <a href="mailto:{{ $customer->email }}" class="users-email-link">{{ $customer->email }}</a>
                    <button type="button" class="users-copy-btn" data-copy="{{ $customer->email }}" title="نسخ البريد">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($customer->segment)
                <span class="users-badge users-badge--role">{{ $customer->segment->name }}</span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ number_format($customer->loyalty_points) }}</span>
        </td>
        <td>
            @if ($customer->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('customer-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.customers.show', $customer) }}"
                        title="عرض العميل">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
                @can('customer-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.customers.edit', $customer) }}"
                        title="تعديل العميل">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('customer-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.customers.destroy', $customer) }}"
                        data-delete-title="حذف العميل"
                        data-delete-message="هل أنت متأكد من حذف هذا العميل؟"
                        data-delete-item="{{ $customer->name }}"
                        title="حذف العميل">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا يوجد عملاء — شغّل SalesSeeder أو أضف عميلاً جديداً</td>
    </tr>
@endforelse
