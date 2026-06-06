@php
    $referenceLabels = [
        'SaleInvoice' => 'فاتورة بيع',
        'PurchaseInvoice' => 'فاتورة شراء',
        'CashVoucher' => 'سند قبض/صرف',
        'SaleReturn' => 'مرتجع بيع',
        'PurchaseReturn' => 'مرتجع شراء',
    ];
@endphp

@forelse ($entries as $e)
    @php
        $refBase = $e->reference_type ? class_basename($e->reference_type) : null;
        $refLabel = $refBase ? ($referenceLabels[$refBase] ?? $refBase) : null;
    @endphp
    <tr>
        <th scope="row" class="users-row-index">{{ $entries->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role" dir="ltr">{{ $e->entry_number }}</span>
        </td>
        <td>{{ $e->entry_date->format('Y-m-d') }}</td>
        <td>
            <span class="users-user-name" style="cursor: default;" title="{{ $e->description }}">
                {{ \Illuminate\Support\Str::limit($e->description, 50) }}
            </span>
        </td>
        <td>
            @if ($refLabel)
                <span class="users-badge users-badge--role">{{ $refLabel }} #{{ $e->reference_id }}</span>
            @else
                —
            @endif
        </td>
        <td>{{ $e->createdBy->name ?? '—' }}</td>
        <td>
            <div class="users-actions">
                @can('journal-entry-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.journal-entries.show', $e) }}"
                        title="عرض القيد">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد قيود</td>
    </tr>
@endforelse
