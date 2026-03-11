@extends('admin.layouts.master')

@section('page-title')
لوحة التحكم
@stop

@section('content')
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
                <h4 class="mb-0">مرحباً بك في لوحة التحكم</h4>
                <p class="mb-0 text-muted">هذه الصفحة تعرض لمحة سريعة عن أهم مؤشرات النظام مع اختصارات للصفحات الرئيسية.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-cash-register fs-24 text-primary"></i>
                        </div>
                        <h6 class="mb-2">مبيعات اليوم (صافي)</h6>
                        <h4 class="fw-bold mb-1">
                            {{ number_format($salesToday['net_sales'] ?? 0, 2) }}
                        </h4>
                        <p class="mb-0 text-muted fs-12">
                            عدد الفواتير: {{ $salesToday['invoices_count'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-shopping-cart fs-24 text-danger"></i>
                        </div>
                        <h6 class="mb-2">مشتريات اليوم (صافي)</h6>
                        <h4 class="fw-bold mb-1">
                            {{ number_format($purchasesToday['net_purchases'] ?? 0, 2) }}
                        </h4>
                        <p class="mb-0 text-muted fs-12">
                            عدد الفواتير: {{ $purchasesToday['invoices_count'] ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-chart-line fs-24 text-success"></i>
                        </div>
                        <h6 class="mb-2">الربح الإجمالي هذا الشهر</h6>
                        <h4 class="fw-bold mb-1">
                            {{ number_format($profitSummary['gross_profit'] ?? 0, 2) }}
                        </h4>
                        <p class="mb-0 text-muted fs-12">
                            الفترة من {{ $profitSummary['from'] ?? '' }} إلى {{ $profitSummary['to'] ?? '' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-users fs-24 text-warning"></i>
                        </div>
                        <h6 class="mb-2">عدد العملاء</h6>
                        <h4 class="fw-bold mb-1">
                            {{ number_format($customersCount ?? 0) }}
                        </h4>
                        <p class="mb-0 text-muted fs-12">
                            إجمالي عدد العملاء المسجلين في النظام
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @can('sale-invoice-list')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.sale-invoices.index') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-file-invoice-dollar fs-24 text-primary"></i>
                        </div>
                        <h6 class="mb-2 text-dark">فواتير البيع</h6>
                        <p class="mb-0 text-muted fs-12">إنشاء وإدارة فواتير البيع والمرتجعات</p>
                    </div>
                </a>
            </div>
            @endcan

            @can('purchase-invoice-list')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.purchase-invoices.index') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-file-invoice fs-24 text-danger"></i>
                        </div>
                        <h6 class="mb-2 text-dark">فواتير الشراء</h6>
                        <p class="mb-0 text-muted fs-12">إدارة فواتير الشراء من الموردين</p>
                    </div>
                </a>
            </div>
            @endcan

            @can('stock-balance-list')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.stock.balances.index') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-warehouse fs-24 text-success"></i>
                        </div>
                        <h6 class="mb-2 text-dark">المخزون</h6>
                        <p class="mb-0 text-muted fs-12">عرض أرصدة المخزون والحركات</p>
                    </div>
                </a>
            </div>
            @endcan

            @canany(['treasury-list', 'bank-account-list'])
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.treasuries.index') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-piggy-bank fs-24 text-warning"></i>
                        </div>
                        <h6 class="mb-2 text-dark">الخزائن والحسابات البنكية</h6>
                        <p class="mb-0 text-muted fs-12">إدارة الخزائن والحسابات البنكية والتحويلات</p>
                    </div>
                </a>
            </div>
            @endcanany

            @can('customer-list')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.customers.index') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-user-friends fs-24 text-info"></i>
                        </div>
                        <h6 class="mb-2 text-dark">العملاء</h6>
                        <p class="mb-0 text-muted fs-12">إدارة العملاء وأرصدتهم وكشوف الحساب</p>
                    </div>
                </a>
            </div>
            @endcan

            @can('supplier-list')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.suppliers.index') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-truck-loading fs-24 text-secondary"></i>
                        </div>
                        <h6 class="mb-2 text-dark">الموردون</h6>
                        <p class="mb-0 text-muted fs-12">إدارة الموردين وأرصدتهم وكشوف الحساب</p>
                    </div>
                </a>
            </div>
            @endcan

            @can('reports-sales')
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 mb-3">
                <a href="{{ route('admin.reports.sales.daily') }}" class="card text-center h-100 text-decoration-none">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fas fa-chart-bar fs-24 text-primary"></i>
                        </div>
                        <h6 class="mb-2 text-dark">التقارير</h6>
                        <p class="mb-0 text-muted fs-12">تقارير المبيعات والمشتريات والأرباح والمخزون</p>
                    </div>
                </a>
            </div>
            @endcan
        </div>
    </div>
</div>
@stop

