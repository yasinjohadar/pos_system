@forelse ($coupons as $c)
    <tr>
        <th scope="row" class="users-row-index">{{ $coupons->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role">{{ $c->code }}</span>
        </td>
        <td>{{ $c->type === 'percent' ? 'نسبة %' : 'مبلغ ثابت' }}</td>
        <td>
            @if ($c->type === 'percent')
                <span class="users-amount">{{ $c->value }}%</span>
            @else
                <span class="users-amount">{{ number_format($c->value, 2) }}</span>
            @endif
        </td>
        <td>
            @if ($c->min_purchase)
                <span class="users-amount">{{ number_format($c->min_purchase, 2) }}</span>
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            {{ $c->used_count }}@if ($c->max_uses) / {{ $c->max_uses }}@endif
        </td>
        <td>
            @if ($c->valid_from || $c->valid_until)
                {{ $c->valid_from?->format('Y-m-d') ?? '—' }} / {{ $c->valid_until?->format('Y-m-d') ?? '—' }}
            @else
                <span class="users-muted-text">—</span>
            @endif
        </td>
        <td>
            @if ($c->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">غير نشط</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('coupon-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.coupons.edit', $c) }}"
                        title="تعديل الكوبون">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('coupon-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.coupons.destroy', $c) }}"
                        data-delete-title="حذف الكوبون"
                        data-delete-message="هل أنت متأكد من حذف هذا الكوبون؟"
                        data-delete-item="{{ $c->code }}"
                        title="حذف الكوبون">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="users-empty">لا توجد كوبونات</td>
    </tr>
@endforelse
