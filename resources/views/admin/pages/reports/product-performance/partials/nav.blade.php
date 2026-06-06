@php
    $active = $active ?? 'index';
    $tabs = [
        'index' => [
            'label' => 'تقرير الأداء',
            'route' => route('admin.reports.product-performance.index', request()->only(['from', 'to'])),
            'icon' => 'fa-chart-line',
        ],
        'top' => [
            'label' => 'أفضل المنتجات',
            'route' => route('admin.reports.product-performance.top', request()->only(['from', 'to', 'limit'])),
            'icon' => 'fa-trophy',
        ],
        'no-sales' => [
            'label' => 'منتجات بدون مبيعات',
            'route' => route('admin.reports.product-performance.no-sales'),
            'icon' => 'fa-box-open',
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
