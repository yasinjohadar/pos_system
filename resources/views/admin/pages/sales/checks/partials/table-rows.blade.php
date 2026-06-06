@forelse ($checks as $c)
    <tr>
        <th scope="row" class="users-row-index">{{ $checks->firstItem() + $loop->index }}</th>
        <td>
            <span class="users-badge users-badge--role">{{ $c->check_number }}</span>
        </td>
        <td>{{ $c->bank_account_id ? ($c->bankAccount->name ?? '—') : ($c->bank_name ?? '—') }}</td>
        <td>
            <span class="users-amount">{{ number_format($c->amount, 2) }}</span>
        </td>
        <td>{{ $c->due_date->format('Y-m-d') }}</td>
        <td>
            @if ($c->status === \App\Models\Check::STATUS_UNDER_COLLECTION)
                <span class="users-badge users-badge--role" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">تحت التحصيل</span>
            @elseif ($c->status === \App\Models\Check::STATUS_COLLECTED)
                <span class="users-badge users-badge--active">محصل</span>
            @else
                <span class="users-badge users-badge--inactive">مرتجع</span>
            @endif
        </td>
        <td>
            <div class="users-actions">
                @can('check-show')
                    <a class="users-action-btn users-action-btn--view"
                        href="{{ route('admin.checks.show', $c) }}"
                        title="عرض الشيك">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد شيكات</td>
    </tr>
@endforelse
