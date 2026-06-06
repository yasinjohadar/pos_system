@forelse ($reconciliations as $rec)
    <tr>
        <th scope="row" class="users-row-index">{{ $reconciliations->firstItem() + $loop->index }}</th>
        <td>{{ $rec->bankAccount->name ?? '—' }}</td>
        <td>{{ $rec->statement_date->format('Y-m-d') }}</td>
        <td><span class="users-amount">{{ number_format($rec->statement_balance, 2) }}</span></td>
        <td><span class="users-amount">{{ number_format($rec->book_balance, 2) }}</span></td>
        <td>
            <span class="users-amount {{ abs($rec->difference) < 0.01 ? 'users-qty--in' : 'users-qty--out' }}">
                {{ number_format($rec->difference, 2) }}
            </span>
        </td>
        <td>
            @if ($rec->status === \App\Models\BankReconciliation::STATUS_RECONCILED)
                <span class="users-badge users-badge--active">مُقفلة</span>
            @else
                <span class="users-badge users-badge--role">مسودة</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('bank-reconciliation-show')
                    <a class="users-action-btn users-action-btn--view" href="{{ route('admin.bank-reconciliations.show', $rec) }}" title="عرض">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="users-empty">لا توجد تسويات بنكية</td>
    </tr>
@endforelse
