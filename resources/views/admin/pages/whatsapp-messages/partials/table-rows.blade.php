@php
    use App\Models\WhatsAppMessage;
@endphp

@forelse ($messages as $message)
    <tr>
        <th scope="row" class="users-row-index">{{ $messages->firstItem() ? $messages->firstItem() + $loop->index : $message->id }}</th>
        <td>
            @if ($message->direction === WhatsAppMessage::DIRECTION_INBOUND)
                <span class="users-badge users-badge--role">واردة</span>
            @else
                <span class="users-badge users-badge--active">صادرة</span>
            @endif
        </td>
        <td>
            <div class="users-user-cell">
                <div class="users-avatar">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <span class="users-user-name" dir="ltr">{{ $message->contact->wa_id ?? '—' }}</span>
            </div>
        </td>
        <td class="users-muted-text">{{ \Illuminate\Support\Str::limit($message->body ?? '—', 50) }}</td>
        <td>
            @if ($message->status === WhatsAppMessage::STATUS_SENT)
                <span class="users-badge users-badge--active">مرسل</span>
            @elseif ($message->status === WhatsAppMessage::STATUS_DELIVERED)
                <span class="users-badge users-badge--role">مستلم</span>
            @elseif ($message->status === WhatsAppMessage::STATUS_READ)
                <span class="users-badge users-badge--role">مقروء</span>
            @elseif ($message->status === WhatsAppMessage::STATUS_FAILED)
                <span class="users-badge users-badge--inactive">فشل</span>
            @elseif ($message->status === WhatsAppMessage::STATUS_QUEUED)
                <span class="users-badge backup-badge--running">في الانتظار</span>
            @else
                <span class="users-badge users-badge--role">{{ $message->status }}</span>
            @endif
        </td>
        <td class="users-muted-text">{{ $message->created_at->format('Y-m-d H:i') }}</td>
        <td>
            <div class="users-actions">
                <a href="{{ route('admin.whatsapp-messages.show', $message) }}"
                    class="users-action-btn users-action-btn--view" title="عرض">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="users-empty">لا توجد رسائل.</td>
    </tr>
@endforelse
