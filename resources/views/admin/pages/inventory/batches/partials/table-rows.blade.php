@forelse ($batches as $batch)
    <tr>
        <th scope="row" class="users-row-index">{{ $batches->firstItem() + $loop->index }}</th>
        <td>{{ $batch->product->name ?? '—' }}</td>
        <td><span class="users-badge users-badge--role">{{ $batch->batch_number }}</span></td>
        <td>{{ $batch->warehouse->name ?? '—' }}</td>
        <td>{{ $batch->received_date->format('Y-m-d') }}</td>
        <td>{{ $batch->expiry_date ? $batch->expiry_date->format('Y-m-d') : '—' }}</td>
        <td>
            <span class="users-amount {{ $batch->current_quantity > 0 ? 'users-qty--in' : 'users-qty--out' }}">
                {{ number_format($batch->current_quantity, 4) }}
            </span>
        </td>
        <td>
            <div class="users-actions">
                @can('product-batch-edit')
                    <a class="users-action-btn users-action-btn--edit" href="{{ route('admin.product-batches.edit', $batch) }}" title="تعديل">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr><td colspan="8" class="users-empty">لا توجد دفعات</td></tr>
@endforelse
