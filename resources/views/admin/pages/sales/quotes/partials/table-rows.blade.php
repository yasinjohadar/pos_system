@forelse ($quotes as $quote)
    <tr>
        <th scope="row" class="users-row-index">{{ $quotes->firstItem() + $loop->index }}</th>
        <td><span class="users-badge users-badge--role">{{ $quote->number }}</span></td>
        <td>{{ $quote->quote_date->format('Y-m-d') }}</td>
        <td>{{ $quote->customer->name ?? '—' }}</td>
        <td>{{ $quote->branch->name ?? '—' }}</td>
        <td><span class="users-amount">{{ number_format($quote->total, 2) }}</span></td>
        <td>
            @php
                $statusLabels = ['draft' => 'مسودة', 'sent' => 'مُرسل', 'accepted' => 'مقبول', 'converted' => 'محوّل', 'cancelled' => 'ملغى'];
            @endphp
            <span class="users-badge users-badge--role">{{ $statusLabels[$quote->status] ?? $quote->status }}</span>
        </td>
        <td>
            <div class="users-actions">
                @can('sales-quote-show')
                    <a class="users-action-btn users-action-btn--view" href="{{ route('admin.sales-quotes.show', $quote) }}" title="عرض">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                @endcan
            </div>
        </td>
    </tr>
@empty
    <tr><td colspan="8" class="users-empty">لا توجد عروض أسعار</td></tr>
@endforelse
