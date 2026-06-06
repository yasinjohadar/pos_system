@forelse ($promotions as $promotion)
    <tr>
        <th scope="row" class="users-row-index">{{ $promotions->firstItem() + $loop->index }}</th>
        <td>{{ $promotion->name }}</td>
        <td>{{ $promotion->type === 'percent' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</td>
        <td>
            @if ($promotion->type === 'percent')
                <span class="users-amount">{{ $promotion->value }}%</span>
            @else
                <span class="users-amount">{{ number_format($promotion->value, 2) }}</span>
            @endif
        </td>
        <td>
            @if ($promotion->start_date || $promotion->end_date)
                {{ $promotion->start_date ? $promotion->start_date->format('Y-m-d') : 'بدون بداية' }}
                —
                {{ $promotion->end_date ? $promotion->end_date->format('Y-m-d') : 'بدون نهاية' }}
            @else
                <span class="users-muted-text">مستمر</span>
            @endif
        </td>
        <td>
            {{ $promotion->min_qty !== null ? number_format($promotion->min_qty, 2) : '—' }}
        </td>
        <td>
            @if ($promotion->is_active)
                <span class="users-badge users-badge--active">نشط</span>
            @else
                <span class="users-badge users-badge--inactive">متوقف</span>
            @endif
        </td>
        <td>
            <span class="users-badge users-badge--role">{{ $promotion->items_count }}</span>
        </td>
        <td>
            <div class="users-actions">
                @can('promotion-edit')
                    <a class="users-action-btn users-action-btn--edit"
                        href="{{ route('admin.promotions.edit', $promotion) }}"
                        title="تعديل العرض">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
                @can('promotion-delete')
                    <button type="button" class="users-action-btn users-action-btn--delete"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-delete-action="{{ route('admin.promotions.destroy', $promotion) }}"
                        data-delete-title="حذف العرض"
                        data-delete-message="هل أنت متأكد من حذف هذا العرض؟"
                        data-delete-item="{{ $promotion->name }}"
                        title="حذف العرض">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="users-empty">لا توجد عروض حالياً</td>
    </tr>
@endforelse
