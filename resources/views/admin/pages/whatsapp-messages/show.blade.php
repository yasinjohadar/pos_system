@extends('admin.layouts.master')

@section('page-title')
    تفاصيل الرسالة
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    @php
        use App\Models\WhatsAppMessage;
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">تفاصيل الرسالة #{{ $message->id }}</h5>
                    <div class="users-header-actions">
                        @if (in_array($message->status, [WhatsAppMessage::STATUS_QUEUED, WhatsAppMessage::STATUS_FAILED]))
                            <form action="{{ route('admin.whatsapp-messages.retry', $message) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="users-btn-edit">
                                    <i class="fas fa-redo"></i>
                                    إعادة المحاولة
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.whatsapp-messages.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i>
                            رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fab fa-whatsapp"></i>
                                معلومات الرسالة
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-exchange-alt"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الاتجاه</span>
                                        <div class="users-detail-item__value">
                                            @if ($message->direction === WhatsAppMessage::DIRECTION_INBOUND)
                                                <span class="users-badge users-badge--role">واردة</span>
                                            @else
                                                <span class="users-badge users-badge--active">صادرة</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-phone"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">المستقبل</span>
                                        <div class="users-detail-item__value users-color-value">
                                            {{ $message->contact->wa_id ?? '—' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-info-circle"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
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
                                        </div>
                                    </div>
                                </div>

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-tag"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">النوع</span>
                                        <div class="users-detail-item__value">{{ $message->type }}</div>
                                    </div>
                                </div>

                                @if ($message->meta_message_id)
                                    <div class="users-detail-item">
                                        <div class="users-detail-item__icon"><i class="fas fa-fingerprint"></i></div>
                                        <div class="users-detail-item__content">
                                            <span class="users-detail-item__label">Meta Message ID</span>
                                            <div class="users-detail-item__value users-color-value">{{ $message->meta_message_id }}</div>
                                        </div>
                                    </div>
                                @endif

                                <div class="users-detail-item">
                                    <div class="users-detail-item__icon"><i class="fas fa-calendar"></i></div>
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">تاريخ الإنشاء</span>
                                        <div class="users-detail-item__value">{{ $message->created_at->format('Y-m-d H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="users-detail-card">
                        <div class="users-detail-card__header">
                            <h6 class="users-detail-card__title">
                                <i class="fas fa-comment-dots"></i>
                                محتوى الرسالة
                            </h6>
                        </div>
                        <div class="users-detail-card__body">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $message->body ?? '—' }}</p>
                        </div>
                    </div>

                    @if ($message->error)
                        <div class="users-detail-card">
                            <div class="users-detail-card__header">
                                <h6 class="users-detail-card__title">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    تفاصيل الخطأ
                                </h6>
                            </div>
                            <div class="users-detail-card__body">
                                <pre class="users-color-value mb-0" style="white-space: pre-wrap;">{{ json_encode($message->error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
