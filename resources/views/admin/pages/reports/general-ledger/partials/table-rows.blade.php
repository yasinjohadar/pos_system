@forelse ($lines as $line)
    @php
        $account = $line->account ?? \App\Models\ChartOfAccount::find($line->account_id);
        $entryDate = $line->entry_date instanceof \Carbon\Carbon ? $line->entry_date : \Carbon\Carbon::parse($line->entry_date);
    @endphp
    <tr>
        <td>{{ $entryDate->format('Y-m-d') }}</td>
        <td><span class="users-badge users-badge--role" dir="ltr">{{ $line->entry_number }}</span></td>
        <td>{{ $account->name ?? '—' }}</td>
        <td>{{ $line->description ?: ($line->entry_description ?? '—') }}</td>
        <td>
            @if ((float) $line->debit > 0)
                <span class="users-amount users-qty--in">{{ number_format($line->debit, 2) }}</span>
            @else — @endif
        </td>
        <td>
            @if ((float) $line->credit > 0)
                <span class="users-amount users-qty--out">{{ number_format($line->credit, 2) }}</span>
            @else — @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="users-empty">لا توجد حركات في الفترة المحددة</td>
    </tr>
@endforelse
