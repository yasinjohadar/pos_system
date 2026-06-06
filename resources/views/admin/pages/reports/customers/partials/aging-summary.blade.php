@php
    $bucketLabels = [
        '0_30' => ['label' => '0–30 يوم', 'icon' => 'fa-clock', 'class' => 'users-qty--in'],
        '31_60' => ['label' => '31–60 يوم', 'icon' => 'fa-hourglass-half', 'class' => ''],
        '61_90' => ['label' => '61–90 يوم', 'icon' => 'fa-hourglass-end', 'class' => 'users-qty--out'],
        '90_plus' => ['label' => 'أكثر من 90 يوم', 'icon' => 'fa-exclamation-triangle', 'class' => 'users-qty--out'],
    ];
    $total = $aging ? array_sum($aging) : 0;
@endphp

@if ($customer && $aging)
    <div class="users-detail-card" style="margin-bottom: 1.25rem;">
        <div class="users-detail-card__body" style="padding: 1.25rem;">
            <div class="users-detail-item" style="margin: 0;">
                <div class="users-detail-item__icon"><i class="fas fa-user"></i></div>
                <div class="users-detail-item__content">
                    <span class="users-detail-item__label">العميل</span>
                    <div class="users-detail-item__value">
                        <span class="users-user-name" style="cursor: default; font-size: 1.125rem;">{{ $customer->name }}</span>
                        @if ($customer->phone)
                            <small class="users-muted-text d-block" dir="ltr">{{ $customer->phone }}</small>
                        @endif
                    </div>
                </div>
                <div class="users-detail-item__content text-end">
                    <span class="users-detail-item__label">إجمالي الرصيد المستحق</span>
                    <div class="users-detail-item__value">
                        <span class="users-amount users-qty--out" style="font-size: 1.25rem; font-weight: 700;">{{ number_format($total, 2) }}</span>
                        <small class="users-muted-text d-block" style="margin-top: 0.35rem;">حتى {{ $asOfDate->format('Y-m-d') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="users-report-kpi-grid">
        @foreach ($bucketLabels as $key => $meta)
            <div class="users-detail-card">
                <div class="users-detail-card__body" style="padding: 1.25rem;">
                    <div class="users-detail-item" style="margin: 0;">
                        <div class="users-detail-item__icon"><i class="fas {{ $meta['icon'] }}"></i></div>
                        <div class="users-detail-item__content">
                            <span class="users-detail-item__label">{{ $meta['label'] }}</span>
                            <div class="users-detail-item__value">
                                <span class="users-amount {{ $meta['class'] }}" style="font-size: 1.125rem; font-weight: 700;">{{ number_format($aging[$key], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="users-table-card" style="margin-top: 1.25rem;">
        <div class="table-responsive">
            <table class="users-table">
                <thead>
                    <tr>
                        @foreach ($bucketLabels as $meta)
                            <th>{{ $meta['label'] }}</th>
                        @endforeach
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        @foreach ($bucketLabels as $key => $meta)
                            <td><span class="users-amount {{ $meta['class'] }}">{{ number_format($aging[$key], 2) }}</span></td>
                        @endforeach
                        <td><span class="users-amount users-qty--out" style="font-weight: 700;">{{ number_format($total, 2) }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    @can('customer-show')
        <div style="margin-top: 1rem;">
            <a href="{{ route('admin.customers.statement', $customer) }}" class="users-btn-secondary">
                <i class="fas fa-file-invoice"></i> كشف حساب العميل
            </a>
        </div>
    @endcan
@else
    <div class="users-table-card">
        <div class="users-empty" style="padding: 2.5rem 1rem;">
            اختر عميلاً واضغط «عرض» — جرّب <strong>شركة أعمار الديون — تجريبي</strong> بعد تشغيل <code>SalesSeeder</code>
        </div>
    </div>
@endif
