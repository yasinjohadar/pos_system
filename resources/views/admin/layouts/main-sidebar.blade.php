        <!-- Start::app-sidebar -->
        <aside class="app-sidebar sticky" id="sidebar">

            <!-- Start::main-sidebar-header -->
            <div class="main-sidebar-header">
                <a href="/" class="header-logo">
                    <svg class="desktop-logo" width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
                        <rect width="120" height="40" fill="#4f46e5" rx="4"/>
                        <text x="60" y="25" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="white" text-anchor="middle">لوحة التحكم</text>
                    </svg>
                    <svg class="toggle-logo" width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" fill="#4f46e5" rx="4"/>
                        <text x="20" y="25" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="white" text-anchor="middle">LD</text>
                    </svg>
                    <svg class="desktop-white" width="120" height="40" viewBox="0 0 120 40" xmlns="http://www.w3.org/2000/svg">
                        <rect width="120" height="40" fill="#ffffff" rx="4"/>
                        <text x="60" y="25" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="#1f2937" text-anchor="middle">لوحة التحكم</text>
                    </svg>
                    <svg class="toggle-white" width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                        <rect width="40" height="40" fill="#ffffff" rx="4"/>
                        <text x="20" y="25" font-family="Arial, sans-serif" font-size="12" font-weight="bold" fill="#1f2937" text-anchor="middle">LD</text>
                    </svg>
                </a>
            </div>
            <!-- End::main-sidebar-header -->

            <!-- Start::main-sidebar -->
            <div class="main-sidebar" id="sidebar-scroll">

                <!-- Start::nav -->
                <nav class="main-menu-container nav nav-pills flex-column sub-open">
                    <div class="slide-left" id="slide-left">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M13.293 6.293 7.586 12l5.707 5.707 1.414-1.414L10.414 12l4.293-4.293z"></path> </svg>
                    </div>
                    <ul class="main-menu">
                        <!-- Start::slide__category -->
                        <li class="slide__category"><span class="category-name">مركز الإدارة</span></li>
                        <!-- End::slide__category -->

                        <!-- Start::slide -->
                        <li class="slide">
                            <a href="/" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg>
                                <span class="side-menu__label">الصفحة الرئيسية</span>
                                <span class="badge bg-success ms-auto menu-badge">1</span>
                            </a>
                        </li>

                              <li class="slide">
                                    <a href="{{route("roles.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                            <path d="M9 12l2 2 4-4"/>
                                        </svg>
                                        <span class="side-menu__label">الصلاحيات</span>
                                    </a>
                                </li>
                                <li class="slide">
                                    <a href="{{route("users.index")}}" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                        <span class="side-menu__label">المستخدمون</span>
                                    </a>
                                </li>
                                <!-- إدارة الفروع والمخازن -->
                                <li class="slide has-sub {{ request()->routeIs('admin.branches.*') || request()->routeIs('admin.warehouses.*') ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                            <polyline points="9 22 9 12 15 12 15 22"/>
                                        </svg>
                                        <span class="side-menu__label">إدارة الفروع</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.branches.index') }}" class="side-menu__item">الفروع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.warehouses.index') }}" class="side-menu__item">المخازن</a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- إدارة المنتجات -->
                                <li class="slide has-sub {{ request()->routeIs('admin.categories.*') || request()->routeIs('admin.units.*') || request()->routeIs('admin.products.*') ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                            <line x1="3" y1="6" x2="21" y2="6"/>
                                            <path d="M16 10a4 4 0 0 1-8 0"/>
                                        </svg>
                                        <span class="side-menu__label">إدارة المنتجات</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.categories.index') }}" class="side-menu__item">التصنيفات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.units.index') }}" class="side-menu__item">الوحدات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.products.index') }}" class="side-menu__item">المنتجات</a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- إدارة المخزون -->
                                <li class="slide has-sub {{ request()->routeIs('admin.stock.*') ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                            <polyline points="7.5 4.21 12 6.81 16.5 4.21"/>
                                            <polyline points="7.5 19.79 7.5 14.6 3 12"/>
                                        </svg>
                                        <span class="side-menu__label">إدارة المخزون</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide {{ request()->routeIs('admin.stock.movements.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.stock.movements.index') }}" class="side-menu__item">حركات المخزون</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.stock.balances.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.stock.balances.index') }}" class="side-menu__item">أرصدة المخزون</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.stock.transfers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.stock.transfers.index') }}" class="side-menu__item">تحويل المخزون</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.stock.inventory-count.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.stock.inventory-count.index') }}" class="side-menu__item">الجرد</a>
                                        </li>
                                        <li class="slide">
                                            <a href="{{ route('admin.stock.balances.index', ['low_stock' => 1]) }}" class="side-menu__item">تنبيهات انخفاض المخزون</a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- المبيعات -->
                                <li class="slide has-sub {{ request()->routeIs('admin.sale-invoices.*') || request()->routeIs('admin.sale-returns.*') || request()->routeIs('admin.customers.*') || request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.treasuries.*') || request()->routeIs('admin.bank-accounts.*') || request()->routeIs('admin.financial-transfers.*') || request()->routeIs('admin.checks.*') || request()->routeIs('admin.promotions.*') || request()->routeIs('admin.price-lists.*') ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                                            <line x1="3" y1="6" x2="21" y2="6"/>
                                            <path d="M16 10a4 4 0 0 1-8 0"/>
                                        </svg>
                                        <span class="side-menu__label">المبيعات</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide {{ request()->routeIs('admin.sale-invoices.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.sale-invoices.index') }}" class="side-menu__item">فواتير البيع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.sale-returns.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.sale-returns.index') }}" class="side-menu__item">مرتجعات البيع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.promotions.index') }}" class="side-menu__item">العروض والخصومات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.price-lists.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.price-lists.index') }}" class="side-menu__item">قوائم الأسعار</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.customers.index') }}" class="side-menu__item">العملاء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.payment-methods.index') }}" class="side-menu__item">طرق الدفع</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.treasuries.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.treasuries.index') }}" class="side-menu__item">الخزائن والبنوك</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.bank-accounts.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.bank-accounts.index') }}" class="side-menu__item">الحسابات البنكية</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.financial-transfers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.financial-transfers.index') }}" class="side-menu__item">التحويلات المالية</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.checks.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.checks.index') }}" class="side-menu__item">الشيكات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.cash-vouchers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.cash-vouchers.index') }}" class="side-menu__item">سندات القبض والصرف</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.fiscal-years.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.fiscal-years.index') }}" class="side-menu__item">السنوات المالية</a>
                                        </li>
                                    </ul>
                                </li>
                                <!-- المشتريات -->
                                <li class="slide has-sub {{ request()->routeIs('admin.purchase-invoices.*') || request()->routeIs('admin.purchase-returns.*') || request()->routeIs('admin.suppliers.*') ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                        </svg>
                                        <span class="side-menu__label">المشتريات</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide {{ request()->routeIs('admin.purchase-invoices.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.purchase-invoices.index') }}" class="side-menu__item">فواتير الشراء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.purchase-returns.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.purchase-returns.index') }}" class="side-menu__item">مرتجعات الشراء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.suppliers.index') }}" class="side-menu__item">الموردون</a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- التقارير -->
                                <li class="slide has-sub {{ request()->routeIs('admin.reports.*') ? 'open active' : '' }}">
                                    <a href="javascript:void(0);" class="side-menu__item">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 3h18v18H3z"/><path d="M8 21V9"/><path d="M16 21V5"/>
                                        </svg>
                                        <span class="side-menu__label">التقارير</span>
                                        <i class="fe fe-chevron-right side-menu__angle"></i>
                                    </a>
                                    <ul class="slide-menu child1">
                                        <li class="slide {{ request()->routeIs('admin.reports.sales.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.sales.daily') }}" class="side-menu__item">تقرير المبيعات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.purchases.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.purchases.daily') }}" class="side-menu__item">تقرير المشتريات</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.profit.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.profit.index') }}" class="side-menu__item">تقرير الأرباح</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.inventory.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.inventory.index') }}" class="side-menu__item">تقرير المخزون</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.inventory.reorder') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.inventory.reorder') }}" class="side-menu__item">تنبيهات إعادة الطلب</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.customers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.customers.aging') }}" class="side-menu__item">تقارير العملاء</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.suppliers.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.suppliers.aging') }}" class="side-menu__item">تقارير الموردين</a>
                                        </li>
                                        <li class="slide {{ request()->routeIs('admin.reports.taxes.*') ? 'active' : '' }}">
                                            <a href="{{ route('admin.reports.taxes.index') }}" class="side-menu__item">التقارير الضريبية</a>
                                        </li>
                                    </ul>
                                </li>

                        <!-- الذكاء الاصطناعي -->
                        <li class="slide has-sub {{ request()->routeIs('admin.ai.*') ? 'open active' : '' }}">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                    <path d="M2 17l10 5 10-5"/>
                                    <path d="M2 12l10 5 10-5"/>
                                </svg>
                                <span class="side-menu__label">الذكاء الاصطناعي</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide {{ request()->routeIs('admin.ai.models.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ai.models.index') }}" class="side-menu__item">نماذج AI</a>
                                </li>
                                <li class="slide {{ request()->routeIs('admin.ai.settings.*') ? 'active' : '' }}">
                                    <a href="{{ route('admin.ai.settings.index') }}" class="side-menu__item">الإعدادات</a>
                                </li>
                            </ul>
                                </li>

                        <!-- End::slide -->



<!-- إعدادات البريد -->
<li class="slide has-sub {{ request()->routeIs('admin.settings.email.*') ? 'open active' : '' }}">
    <a href="javascript:void(0);" class="side-menu__item">
        <i class="ri-mail-settings-line side-menu__icon"></i>
        <span class="side-menu__label">إعدادات البريد</span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide {{ request()->routeIs('admin.settings.email.index') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.email.index') }}" class="side-menu__item {{ request()->routeIs('admin.settings.email.index') ? 'active' : '' }}">جميع الإعدادات</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.settings.email.create') ? 'active' : '' }}">
            <a href="{{ route('admin.settings.email.create') }}" class="side-menu__item {{ request()->routeIs('admin.settings.email.create') ? 'active' : '' }}">إضافة إعدادات</a>
        </li>
    </ul>
</li>

<!-- التخزين السحابي -->
<li class="slide has-sub {{ request()->routeIs('admin.storage.*') ? 'open active' : '' }}">
    <a href="javascript:void(0);" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
            <line x1="12" y1="22.08" x2="12" y2="12"/>
        </svg>
        <span class="side-menu__label">التخزين السحابي</span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide {{ request()->routeIs('admin.storage.index') ? 'active' : '' }}">
            <a href="{{ route('admin.storage.index') }}" class="side-menu__item">أماكن التخزين</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.storage.create') ? 'active' : '' }}">
            <a href="{{ route('admin.storage.create') }}" class="side-menu__item">إضافة مكان تخزين</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.storage.analytics') ? 'active' : '' }}">
            <a href="{{ route('admin.storage.analytics') }}" class="side-menu__item">الإحصائيات</a>
        </li>
    </ul>
</li>

<!-- النسخ الاحتياطي -->
<li class="slide has-sub {{ request()->routeIs('admin.backups.*') || request()->routeIs('admin.backup-schedules.*') || request()->routeIs('admin.backup-storage.*') ? 'open active' : '' }}">
    <a href="javascript:void(0);" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
            <polyline points="17 21 17 13 7 13 7 21"/>
            <polyline points="7 3 7 8 15 8"/>
        </svg>
        <span class="side-menu__label">النسخ الاحتياطي</span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide {{ request()->routeIs('admin.backups.*') ? 'active' : '' }}">
            <a href="{{ route('admin.backups.index') }}" class="side-menu__item">النسخ الاحتياطية</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.backups.create') ? 'active' : '' }}">
            <a href="{{ route('admin.backups.create') }}" class="side-menu__item">إنشاء نسخة</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.backup-schedules.*') ? 'active' : '' }}">
            <a href="{{ route('admin.backup-schedules.index') }}" class="side-menu__item">الجداول الزمنية</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.backup-storage.*') ? 'active' : '' }}">
            <a href="{{ route('admin.backup-storage.index') }}" class="side-menu__item">إعدادات التخزين</a>
        </li>
    </ul>
</li>

<!-- إعدادات التخزين -->
<li class="slide {{ request()->routeIs('admin.storage-disk-mappings.*') ? 'active' : '' }}">
    <a href="{{ route('admin.storage-disk-mappings.index') }}" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="2" x2="12" y2="6"/>
            <line x1="12" y1="18" x2="12" y2="22"/>
            <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/>
            <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/>
            <line x1="2" y1="12" x2="6" y2="12"/>
            <line x1="18" y1="12" x2="22" y2="12"/>
            <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/>
            <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/>
        </svg>
        <span class="side-menu__label">ربط الأقراص</span>
    </a>
</li>

<!-- WhatsApp -->
<li class="slide has-sub {{ request()->routeIs('admin.whatsapp*') ? 'open active' : '' }}">
    <a href="javascript:void(0);" class="side-menu__item">
        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/>
            <path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0-5 5h1a.5.5 0 0 0 0-1H5a.5.5 0 0 0 0 1h1a5 5 0 0 0 5-5v-1a.5.5 0 0 0-1 0v1z"/>
        </svg>
        <span class="side-menu__label">واتساب</span>
        <i class="fe fe-chevron-right side-menu__angle"></i>
    </a>
    <ul class="slide-menu child1">
        <li class="slide {{ request()->routeIs('admin.whatsapp-messages.*') ? 'active' : '' }}">
            <a href="{{ route('admin.whatsapp-messages.index') }}" class="side-menu__item">الرسائل</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.whatsapp-settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.whatsapp-settings.index') }}" class="side-menu__item">إعدادات Meta API</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.whatsapp-web.*') ? 'active' : '' }}">
            <a href="{{ route('admin.whatsapp-web.connect') }}" class="side-menu__item">واتساب ويب</a>
        </li>
        <li class="slide {{ request()->routeIs('admin.whatsapp-web-settings.*') ? 'active' : '' }}">
            <a href="{{ route('admin.whatsapp-web-settings.index') }}" class="side-menu__item">إعدادات واتساب ويب</a>
        </li>
    </ul>
</li>





                        {{-- <!-- Start::slide -->
                        <li class="slide has-sub">
                            <a href="javascript:void(0);" class="side-menu__item">
                                <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M4 12c0 4.08 3.06 7.44 7 7.93V4.07C7.05 4.56 4 7.92 4 12z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93s3.05-7.44 7-7.93v15.86zm2-15.86c1.03.13 2 .45 2.87.93H13v-.93zM13 7h5.24c.25.31.48.65.68 1H13V7zm0 3h6.74c.08.33.15.66.19 1H13v-1zm0 9.93V19h2.87c-.87.48-1.84.8-2.87.93zM18.24 17H13v-1h5.92c-.2.35-.43.69-.68 1zm1.5-3H13v-1h6.93c-.04.34-.11.67-.19 1z"/></svg>
                                <span class="side-menu__label">الاعدادات</span>
                                <i class="fe fe-chevron-right side-menu__angle"></i>
                            </a>
                            <ul class="slide-menu child1">
                                <li class="slide side-menu__label1">
                                    <a href="javascript:void(0);">Apps</a>
                                </li>
                                <li class="slide">
                                    <a href="cards.html" class="side-menu__item">الاعدادات العامة</a>
                                </li>
                                <li class="slide">
                                    <a href="{{route("roles.index")}}" class="side-menu__item">الصلاحيات</a>
                                </li>
                                <li class="slide">
                                    <a href="{{route("users.index")}}" class="side-menu__item">المستخدمون</a>
                                </li>

                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- End::slide --> --}}


                    </ul>
                    <div class="slide-right" id="slide-right"><svg xmlns="http://www.w3.org/2000/svg" fill="#7b8191" width="24" height="24" viewBox="0 0 24 24"> <path d="M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"></path> </svg></div>
                </nav>
                <!-- End::nav -->

            </div>
            <!-- End::main-sidebar -->

        </aside>
        <!-- End::app-sidebar -->
