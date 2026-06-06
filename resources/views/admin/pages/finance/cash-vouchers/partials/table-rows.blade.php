@forelse ($vouchers as $v)
    <tr>
        <th scope="row" class="users-row-index">{{ $vouchers->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role">{{ $v->voucher_number }}</span>
        </td>
        <td>{{ $v->date->format('Y-m-d') }}</td>
        <td>
            @if ($v->type === 'receipt')
                <span class="users-badge users-badge--active">قبض</span>
            @else
                <span class="users-badge users-badge--inactive">صرف</span>
            @endif
        </td>
        <td>
            @if ($v->treasury)
                {{ $v->treasury->name }} ({{ $v->treasury->type === 'cashbox' ? 'خزنة' : 'بنك' }})
            @elseif ($v->bankAccount)
                {{ $v->bankAccount->name }}
            @else
                —
            @endif
        </td>
        <td>{{ $v->category ?? '—' }}</td>
        <td>
            <span class="users-amount {{ $v->type === 'receipt' ? 'users-qty--in' : 'users-qty--out' }}">
                {{ number_format($v->amount, 2) }}
            </span>
        </td>
        <td>
            <div class="users-actions">
                @can('cash-voucher-show')
                    <a class="users-action-btn users-action-btn--view" href="{{ route('admin.cash-vouchers.show', $v) }}" title="عرض">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a class="users-action-btn users-action-btn--edit" href="{{ route('admin.cash-vouchers.print', $v) }}" target="_blank" title="طباعة">
                        <i class="fa-solid fa-print"></i>
                    </a>
                @endcan
                @if (!$v->isCancelled())
                    @can('cancel_financial_transaction')
                        <form action="{{ route('admin.cash-vouchers.cancel', $v) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('هل أنت متأكد من إلغاء هذا السند؟');">
                            @csrf
                            <button type="submit" class="users-action-btn users-action-btn--delete" title="إلغاء">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </form>
                    @endcan
                @else
                    <span class="users-badge users-badge--inactive">ملغى</span>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد سندات</td>
    </tr>
@endforelse
