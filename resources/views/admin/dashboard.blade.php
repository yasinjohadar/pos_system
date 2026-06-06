@extends('admin.layouts.master')

@section('page-title')
لوحة التحكم
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('assets/libs/apexcharts/apexcharts.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid p-0">
        <div class="dashboard-premium">

            {{-- ترحيب --}}
            <div class="dash-welcome">
                <h1 class="dash-welcome-title">مرحباً بك في لوحة التحكم</h1>
                <p class="dash-welcome-date">{{ $todayFormatted }}</p>
                <p class="dash-welcome-desc">لمحة سريعة عن أهم مؤشرات النظام مع اختصارات للصفحات الرئيسية.</p>
            </div>

            {{-- KPI Row 1 --}}
            <div class="dash-kpi-grid">
                <div class="dash-kpi-card">
                    <div class="dash-kpi-stripe dash-kpi-stripe--sales"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--sales">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">مبيعات اليوم (صافي)</div>
                            <div class="dash-kpi-value" data-count="{{ $salesToday['net_sales'] ?? 0 }}" data-decimals="2">0</div>
                            <p class="dash-kpi-meta">عدد الفواتير: {{ $salesToday['invoices_count'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="dash-kpi-card">
                    <div class="dash-kpi-stripe dash-kpi-stripe--purchase"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--purchase">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">مشتريات اليوم (صافي)</div>
                            <div class="dash-kpi-value" data-count="{{ $purchasesToday['net_purchases'] ?? 0 }}" data-decimals="2">0</div>
                            <p class="dash-kpi-meta">عدد الفواتير: {{ $purchasesToday['invoices_count'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="dash-kpi-card">
                    <div class="dash-kpi-stripe dash-kpi-stripe--profit"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--profit">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">الربح الإجمالي هذا الشهر</div>
                            <div class="dash-kpi-value" data-count="{{ $profitSummary['gross_profit'] ?? 0 }}" data-decimals="2">0</div>
                            <p class="dash-kpi-meta">من {{ $profitSummary['from'] ?? '' }} إلى {{ $profitSummary['to'] ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <div class="dash-kpi-card">
                    <div class="dash-kpi-stripe dash-kpi-stripe--customer"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--customer">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">عدد العملاء</div>
                            <div class="dash-kpi-value" data-count="{{ $customersCount ?? 0 }}" data-decimals="0">0</div>
                            <p class="dash-kpi-meta">إجمالي العملاء المسجلين</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KPI Row 2 --}}
            <div class="dash-kpi-grid">
                <div class="dash-kpi-card">
                    <div class="dash-kpi-stripe dash-kpi-stripe--balance"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--balance">
                            <i class="fas fa-hand-holding-usd"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">رصيد العملاء (مستحق)</div>
                            <div class="dash-kpi-value" data-count="{{ $customersBalance ?? 0 }}" data-decimals="2">0</div>
                            <p class="dash-kpi-meta">إجمالي المستحقات</p>
                        </div>
                    </div>
                </div>

                <div class="dash-kpi-card">
                    <div class="dash-kpi-stripe dash-kpi-stripe--supplier"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--supplier">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">رصيد الموردين (مستحق)</div>
                            <div class="dash-kpi-value" data-count="{{ $suppliersBalance ?? 0 }}" data-decimals="2">0</div>
                            <p class="dash-kpi-meta">إجمالي المستحقات</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.reports.inventory.reorder') }}" class="dash-kpi-card dash-kpi-card--link">
                    <div class="dash-kpi-stripe dash-kpi-stripe--alert"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--alert {{ ($stockAlertsCount ?? 0) > 0 ? 'pulse' : '' }}">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">تنبيهات إعادة الطلب</div>
                            <div class="dash-kpi-value" data-count="{{ $stockAlertsCount ?? 0 }}" data-decimals="0">0</div>
                            <p class="dash-kpi-meta">منتج تحت الحد الأدنى</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('admin.checks.index') }}" class="dash-kpi-card dash-kpi-card--link">
                    <div class="dash-kpi-stripe dash-kpi-stripe--check"></div>
                    <div class="dash-kpi-body">
                        <div class="dash-kpi-icon dash-kpi-icon--check {{ isset($dueChecks) && $dueChecks->count() > 0 ? 'pulse' : '' }}">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <div class="dash-kpi-content">
                            <div class="dash-kpi-label">شيكات قريبة الاستحقاق</div>
                            <div class="dash-kpi-value" data-count="{{ isset($dueChecks) ? $dueChecks->count() : 0 }}" data-decimals="0">0</div>
                            <p class="dash-kpi-meta">خلال 7 أيام</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- اختصارات سريعة --}}
            <h2 class="dash-shortcuts-title">اختصارات سريعة</h2>
            <div class="dash-shortcuts-grid">
                @can('sale-invoice-list')
                <a href="{{ route('admin.sale-invoices.index') }}" class="dash-shortcut-card">
                    <i class="fas fa-file-invoice-dollar dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--primary">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="dash-shortcut-title">فواتير البيع</div>
                    <p class="dash-shortcut-desc">إنشاء وإدارة فواتير البيع والمرتجعات</p>
                </a>
                @endcan

                @can('purchase-invoice-list')
                <a href="{{ route('admin.purchase-invoices.index') }}" class="dash-shortcut-card">
                    <i class="fas fa-file-invoice dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--danger">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    <div class="dash-shortcut-title">فواتير الشراء</div>
                    <p class="dash-shortcut-desc">إدارة فواتير الشراء من الموردين</p>
                </a>
                @endcan

                @can('stock-balance-list')
                <a href="{{ route('admin.stock.balances.index') }}" class="dash-shortcut-card">
                    <i class="fas fa-warehouse dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--success">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <div class="dash-shortcut-title">المخزون</div>
                    <p class="dash-shortcut-desc">عرض أرصدة المخزون والحركات</p>
                </a>
                @endcan

                @canany(['treasury-list', 'bank-account-list'])
                <a href="{{ route('admin.treasuries.index') }}" class="dash-shortcut-card">
                    <i class="fas fa-piggy-bank dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--warning">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="dash-shortcut-title">الخزائن والحسابات البنكية</div>
                    <p class="dash-shortcut-desc">إدارة الخزائن والحسابات البنكية والتحويلات</p>
                </a>
                @endcanany

                @can('customer-list')
                <a href="{{ route('admin.customers.index') }}" class="dash-shortcut-card">
                    <i class="fas fa-user-friends dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--info">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <div class="dash-shortcut-title">العملاء</div>
                    <p class="dash-shortcut-desc">إدارة العملاء وأرصدتهم وكشوف الحساب</p>
                </a>
                @endcan

                @can('supplier-list')
                <a href="{{ route('admin.suppliers.index') }}" class="dash-shortcut-card">
                    <i class="fas fa-truck-loading dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--secondary">
                        <i class="fas fa-truck-loading"></i>
                    </div>
                    <div class="dash-shortcut-title">الموردون</div>
                    <p class="dash-shortcut-desc">إدارة الموردين وأرصدتهم وكشوف الحساب</p>
                </a>
                @endcan

                @can('reports-sales')
                <a href="{{ route('admin.reports.sales.daily') }}" class="dash-shortcut-card">
                    <i class="fas fa-chart-bar dash-shortcut-bg-icon"></i>
                    <div class="dash-shortcut-icon dash-shortcut-icon--primary">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="dash-shortcut-title">التقارير</div>
                    <p class="dash-shortcut-desc">تقارير المبيعات والمشتريات والأرباح والمخزون</p>
                </a>
                @endcan
            </div>

            {{-- رسم المبيعات والمشتريات --}}
            <div class="dash-chart-card">
                <h2 class="dash-section-title">المبيعات والمشتريات — الشهر الحالي</h2>
                <div id="dash-sales-chart" class="dash-chart-wrap"></div>
            </div>

            {{-- أفضل المنتجات والعملاء --}}
            <div class="dash-analytics-row">
                <div class="dash-chart-card">
                    <h2 class="dash-section-title">أفضل 5 منتجات مبيعاً</h2>
                    <div id="dash-products-chart" class="dash-chart-wrap dash-chart-wrap--sm"></div>
                </div>
                <div class="dash-chart-card">
                    <h2 class="dash-section-title">أفضل 5 عملاء</h2>
                    <div id="dash-customers-chart" class="dash-chart-wrap dash-chart-wrap--sm"></div>
                </div>
            </div>

        </div>
    </div>
</div>
@stop

@section('script')
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script>
    window.dashboardData = {
        salesChart: @json($salesChart),
        purchasesChart: @json($purchasesChart),
        topProductsChart: @json($topProductsChart),
        topCustomersChart: @json($topCustomersChart),
        localeUrl: @json(asset('assets/libs/apexcharts/locales/ar.json')),
    };
</script>
<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@stop
