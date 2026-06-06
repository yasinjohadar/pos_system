@extends('admin.layouts.master')

@section('page-title')
    عرض سعر {{ $salesQuote->number }}
@stop

@section('css')
    @include('admin.components.premium.styles')
@stop

@section('content')
    @php
        $statusLabels = ['draft' => 'مسودة', 'sent' => 'مُرسل', 'accepted' => 'مقبول', 'converted' => 'محوّل', 'cancelled' => 'ملغى'];
    @endphp

    <div class="main-content app-content">
        <div class="container-fluid p-0">
            <div class="users-premium">

                @include('admin.components.premium.flash')

                <div class="users-header">
                    <h5 class="users-page-title">عرض سعر: {{ $salesQuote->number }}</h5>
                    <div class="users-header-actions">
                        @if ($salesQuote->status !== \App\Models\SalesQuote::STATUS_CONVERTED)
                            @can('sales-quote-convert')
                                <form action="{{ route('admin.sales-quotes.convert', $salesQuote) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('تحويل العرض إلى فاتورة بيع؟');">
                                    @csrf
                                    <button type="submit" class="users-btn-create"><i class="fas fa-exchange-alt"></i> تحويل لفاتورة</button>
                                </form>
                            @endcan
                        @endif
                        <a href="{{ route('admin.sales-quotes.index') }}" class="users-btn-secondary">
                            <i class="fas fa-arrow-right"></i> رجوع
                        </a>
                    </div>
                </div>

                <div class="users-detail-grid">
                    <div class="users-detail-card">
                        <div class="users-detail-card__body">
                            <div class="users-detail-list">
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">التاريخ</span>
                                        <div class="users-detail-item__value">{{ $salesQuote->quote_date->format('Y-m-d') }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">العميل</span>
                                        <div class="users-detail-item__value">{{ $salesQuote->customer->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الفرع</span>
                                        <div class="users-detail-item__value">{{ $salesQuote->branch->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الحالة</span>
                                        <div class="users-detail-item__value">
                                            <span class="users-badge users-badge--role">{{ $statusLabels[$salesQuote->status] ?? $salesQuote->status }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="users-detail-item">
                                    <div class="users-detail-item__content">
                                        <span class="users-detail-item__label">الإجمالي</span>
                                        <div class="users-detail-item__value"><span class="users-amount users-qty--in">{{ number_format($salesQuote->total, 2) }}</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="users-table-card" style="margin-top: 1.25rem;">
                    <div class="table-responsive">
                        <table class="users-table">
                            <thead>
                                <tr><th>المنتج</th><th>الكمية</th><th>السعر</th><th>الإجمالي</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($salesQuote->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? '—' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->unit_price, 2) }}</td>
                                        <td><span class="users-amount">{{ number_format($item->total, 2) }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr><td colspan="3" class="text-end"><strong>المجموع الفرعي</strong></td><td>{{ number_format($salesQuote->subtotal, 2) }}</td></tr>
                                <tr><td colspan="3" class="text-end">الضريبة ({{ $salesQuote->tax_rate }}%)</td><td>{{ number_format($salesQuote->tax_amount, 2) }}</td></tr>
                                <tr><td colspan="3" class="text-end"><strong>الإجمالي</strong></td><td><strong>{{ number_format($salesQuote->total, 2) }}</strong></td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section('script')
    @include('admin.components.premium.scripts')
@stop
