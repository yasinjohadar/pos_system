@php
    $typeLabels = [
        'asset' => 'أصول',
        'liability' => 'خصوم',
        'equity' => 'حقوق ملكية',
        'revenue' => 'إيرادات',
        'expense' => 'مصروفات',
    ];
@endphp

@forelse ($rows as $row)
    <tr>
        <td><span class="users-badge users-badge--role" dir="ltr">{{ $row->account_code }}</span></td>
        <td><span class="users-user-name" style="cursor: default;">{{ $row->account_name }}</span></td>
        <td><span class="users-badge users-badge--role">{{ $typeLabels[$row->account_type] ?? $row->account_type }}</span></td>
        <td><span class="users-amount users-qty--in">{{ number_format($row->debit, 2) }}</span></td>
        <td><span class="users-amount users-qty--out">{{ number_format($row->credit, 2) }}</span></td>
        <td>
            @if ($row->balance_debit > 0)
                <span class="users-amount users-qty--in">{{ number_format($row->balance_debit, 2) }}</span>
            @else
                —
            @endif
        </td>
        <td>
            @if ($row->balance_credit > 0)
                <span class="users-amount users-qty--out">{{ number_format($row->balance_credit, 2) }}</span>
            @else
                —
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد حركات في الفترة المحددة</td>
    </tr>
@endforelse

@if (count($rows) > 0)
    <tr style="background: rgba(99, 102, 241, 0.06); font-weight: 600;">
        <td colspan="3" class="text-end" style="padding: 0.875rem 1rem;">المجموع:</td>
        <td><span class="users-amount users-qty--in">{{ number_format($totalDebit, 2) }}</span></td>
        <td><span class="users-amount users-qty--out">{{ number_format($totalCredit, 2) }}</span></td>
        <td colspan="2"></td>
    </tr>
@endif
