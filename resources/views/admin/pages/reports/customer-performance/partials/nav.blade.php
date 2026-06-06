@php
    $active = $active ?? 'index';
    $tabs = [
        'index' => [
            'label' => 'تقرير الأداء',
            'route' => route('admin.reports.customer-performance.index', request()->only(['from', 'to'])),
            'icon' => 'fa-chart-line',
        ],
        'top' => [
            'label' => 'أفضل العملاء',
            'route' => route('admin.reports.customer-performance.top', request()->only(['from', 'to', 'limit'])),
            'icon' => 'fa-trophy',
        ],
        'inactive' => [
            'label' => 'عملاء غير نشطين',
            'route' => route('admin.reports.customer-performance.inactive', request()->only(['days'])),
            'icon' => 'fa-user-clock',
        ],
    ];
@endphp

<div class="users-report-nav">
    @foreach ($tabs as $key => $tab)
        <a href="{{ $tab['route'] }}"
            class="users-report-nav__link {{ $active === $key ? 'is-active' : '' }}">
            <i class="fas {{ $tab['icon'] }}"></i>
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
