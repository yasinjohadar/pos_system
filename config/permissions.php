<?php

return [

    'groups' => [
        'roles' => [
            'label' => 'الأدوار والصلاحيات',
            'icon' => 'fas fa-user-shield',
            'permissions' => ['role-list', 'role-create', 'role-edit', 'role-delete'],
        ],
        'users' => [
            'label' => 'المستخدمون',
            'icon' => 'fas fa-users-cog',
            'permissions' => ['user-list', 'user-create', 'user-edit', 'user-delete', 'user-show'],
        ],
        'branches' => [
            'label' => 'الفروع',
            'icon' => 'fas fa-code-branch',
            'permissions' => ['branch-list', 'branch-create', 'branch-edit', 'branch-delete', 'branch-show'],
        ],
        'warehouses' => [
            'label' => 'المخازن',
            'icon' => 'fas fa-warehouse',
            'permissions' => ['warehouse-list', 'warehouse-create', 'warehouse-edit', 'warehouse-delete', 'warehouse-show'],
        ],
        'categories' => [
            'label' => 'التصنيفات',
            'icon' => 'fas fa-tags',
            'permissions' => ['category-list', 'category-create', 'category-edit', 'category-delete', 'category-show'],
        ],
        'products' => [
            'label' => 'المنتجات',
            'icon' => 'fas fa-box',
            'permissions' => ['product-list', 'product-create', 'product-edit', 'product-delete', 'product-show', 'product-featured'],
        ],
        'units' => [
            'label' => 'الوحدات',
            'icon' => 'fas fa-ruler',
            'permissions' => ['unit-list', 'unit-create', 'unit-edit', 'unit-delete', 'unit-show'],
        ],
        'stock' => [
            'label' => 'إدارة المخزون',
            'icon' => 'fas fa-boxes',
            'permissions' => ['stock-list', 'stock-movement-create', 'stock-transfer-create', 'stock-count'],
        ],
        'payment_methods' => [
            'label' => 'طرق الدفع',
            'icon' => 'fas fa-credit-card',
            'permissions' => ['payment-method-list', 'payment-method-create', 'payment-method-edit', 'payment-method-delete'],
        ],
        'customers' => [
            'label' => 'العملاء',
            'icon' => 'fas fa-user-friends',
            'permissions' => ['customer-list', 'customer-create', 'customer-edit', 'customer-delete', 'customer-show'],
        ],
        'treasuries' => [
            'label' => 'الخزائن',
            'icon' => 'fas fa-piggy-bank',
            'permissions' => ['treasury-list', 'treasury-create', 'treasury-edit', 'treasury-delete'],
        ],
        'bank_accounts' => [
            'label' => 'الحسابات البنكية',
            'icon' => 'fas fa-university',
            'permissions' => ['bank-account-list', 'bank-account-create', 'bank-account-edit', 'bank-account-delete'],
        ],
        'financial_transfers' => [
            'label' => 'التحويلات المالية',
            'icon' => 'fas fa-exchange-alt',
            'permissions' => ['financial-transfer-list', 'financial-transfer-create'],
        ],
        'checks' => [
            'label' => 'الشيكات',
            'icon' => 'fas fa-money-check-alt',
            'permissions' => ['check-list', 'check-create', 'check-edit', 'check-show'],
        ],
        'cash_vouchers' => [
            'label' => 'السندات النقدية',
            'icon' => 'fas fa-file-invoice-dollar',
            'permissions' => ['cash-voucher-list', 'cash-voucher-create', 'cash-voucher-show'],
        ],
        'taxes' => [
            'label' => 'الضرائب',
            'icon' => 'fas fa-percentage',
            'permissions' => ['tax-list', 'tax-create', 'tax-edit', 'tax-delete'],
        ],
        'bank_reconciliation' => [
            'label' => 'التسوية البنكية',
            'icon' => 'fas fa-balance-scale',
            'permissions' => ['bank-reconciliation-list', 'bank-reconciliation-create', 'bank-reconciliation-show'],
        ],
        'fiscal_year' => [
            'label' => 'السنة المالية',
            'icon' => 'fas fa-calendar-alt',
            'permissions' => ['fiscal-year-manage'],
        ],
        'sale_invoices' => [
            'label' => 'فواتير البيع',
            'icon' => 'fas fa-file-invoice',
            'permissions' => ['sale-invoice-list', 'sale-invoice-create', 'sale-invoice-edit', 'sale-invoice-delete', 'sale-invoice-show', 'sale-invoice-confirm'],
        ],
        'sale_returns' => [
            'label' => 'مرتجعات البيع',
            'icon' => 'fas fa-undo',
            'permissions' => ['sale-return-list', 'sale-return-create', 'sale-return-show', 'sale-return-complete'],
        ],
        'coupons' => [
            'label' => 'الكوبونات',
            'icon' => 'fas fa-ticket-alt',
            'permissions' => ['coupon-list', 'coupon-create', 'coupon-edit', 'coupon-delete'],
        ],
        'suppliers' => [
            'label' => 'الموردون',
            'icon' => 'fas fa-truck',
            'permissions' => ['supplier-list', 'supplier-create', 'supplier-edit', 'supplier-delete', 'supplier-show'],
        ],
        'purchase_invoices' => [
            'label' => 'فواتير الشراء',
            'icon' => 'fas fa-shopping-cart',
            'permissions' => ['purchase-invoice-list', 'purchase-invoice-create', 'purchase-invoice-edit', 'purchase-invoice-delete', 'purchase-invoice-show', 'purchase-invoice-confirm'],
        ],
        'purchase_returns' => [
            'label' => 'مرتجعات الشراء',
            'icon' => 'fas fa-undo-alt',
            'permissions' => ['purchase-return-list', 'purchase-return-create', 'purchase-return-show', 'purchase-return-complete'],
        ],
        'reports' => [
            'label' => 'التقارير',
            'icon' => 'fas fa-chart-bar',
            'permissions' => ['reports-sales', 'reports-purchases', 'reports-profit', 'reports-inventory', 'reports-partners', 'reports-taxes', 'reports-view'],
        ],
        'promotions' => [
            'label' => 'العروض الترويجية',
            'icon' => 'fas fa-percent',
            'permissions' => ['promotion-list', 'promotion-create', 'promotion-edit', 'promotion-delete'],
        ],
        'price_lists' => [
            'label' => 'قوائم الأسعار',
            'icon' => 'fas fa-list-alt',
            'permissions' => ['price-list-list', 'price-list-create', 'price-list-edit', 'price-list-delete'],
        ],
        'customer_segments' => [
            'label' => 'شرائح العملاء',
            'icon' => 'fas fa-layer-group',
            'permissions' => ['customer-segment-list', 'customer-segment-create', 'customer-segment-edit', 'customer-segment-delete'],
        ],
        'loyalty' => [
            'label' => 'نقاط الولاء',
            'icon' => 'fas fa-star',
            'permissions' => ['loyalty-list', 'loyalty-adjust'],
        ],
        'accounting' => [
            'label' => 'المحاسبة',
            'icon' => 'fas fa-calculator',
            'permissions' => [
                'chart-of-account-list', 'chart-of-account-create', 'chart-of-account-edit', 'chart-of-account-delete',
                'journal-entry-list', 'journal-entry-show', 'journal-entry-create', 'journal-entry-post', 'journal-entry-reverse',
            ],
        ],
        'pos' => [
            'label' => 'نقطة البيع',
            'icon' => 'fas fa-cash-register',
            'permissions' => ['pos-access'],
        ],
        'sales_quotes' => [
            'label' => 'عروض الأسعار',
            'icon' => 'fas fa-file-alt',
            'permissions' => ['sales-quote-list', 'sales-quote-create', 'sales-quote-show', 'sales-quote-convert'],
        ],
        'purchase_orders' => [
            'label' => 'أوامر الشراء',
            'icon' => 'fas fa-clipboard-list',
            'permissions' => ['purchase-order-list', 'purchase-order-create', 'purchase-order-show', 'purchase-order-convert'],
        ],
        'product_batches' => [
            'label' => 'دفعات المنتجات',
            'icon' => 'fas fa-layer-group',
            'permissions' => ['product-batch-list', 'product-batch-create', 'product-batch-edit'],
        ],
        'attachments' => [
            'label' => 'المرفقات',
            'icon' => 'fas fa-paperclip',
            'permissions' => ['attachment-list', 'attachment-delete'],
        ],
        'advanced' => [
            'label' => 'صلاحيات متقدمة',
            'icon' => 'fas fa-shield-alt',
            'permissions' => ['discount_above_10', 'edit_confirmed_invoice', 'cancel_financial_transaction', 'view_all_branches', 'manage_audit_logs'],
        ],
        'system' => [
            'label' => 'النظام والإعدادات',
            'icon' => 'fas fa-cogs',
            'permissions' => ['dashboard-view', 'settings-manage'],
        ],
    ],

    'labels' => [
        'role-list' => 'عرض الأدوار',
        'role-create' => 'إنشاء دور',
        'role-edit' => 'تعديل دور',
        'role-delete' => 'حذف دور',

        'user-list' => 'عرض المستخدمين',
        'user-create' => 'إنشاء مستخدم',
        'user-edit' => 'تعديل مستخدم',
        'user-delete' => 'حذف مستخدم',
        'user-show' => 'عرض تفاصيل المستخدم',

        'branch-list' => 'عرض الفروع',
        'branch-create' => 'إنشاء فرع',
        'branch-edit' => 'تعديل فرع',
        'branch-delete' => 'حذف فرع',
        'branch-show' => 'عرض تفاصيل الفرع',

        'warehouse-list' => 'عرض المخازن',
        'warehouse-create' => 'إنشاء مخزن',
        'warehouse-edit' => 'تعديل مخزن',
        'warehouse-delete' => 'حذف مخزن',
        'warehouse-show' => 'عرض تفاصيل المخزن',

        'category-list' => 'عرض التصنيفات',
        'category-create' => 'إنشاء تصنيف',
        'category-edit' => 'تعديل تصنيف',
        'category-delete' => 'حذف تصنيف',
        'category-show' => 'عرض تفاصيل التصنيف',

        'product-list' => 'عرض المنتجات',
        'product-create' => 'إنشاء منتج',
        'product-edit' => 'تعديل منتج',
        'product-delete' => 'حذف منتج',
        'product-show' => 'عرض تفاصيل المنتج',
        'product-featured' => 'تمييز المنتج',

        'unit-list' => 'عرض الوحدات',
        'unit-create' => 'إنشاء وحدة',
        'unit-edit' => 'تعديل وحدة',
        'unit-delete' => 'حذف وحدة',
        'unit-show' => 'عرض تفاصيل الوحدة',

        'stock-list' => 'عرض المخزون',
        'stock-movement-create' => 'إنشاء حركة مخزون',
        'stock-transfer-create' => 'إنشاء تحويل مخزون',
        'stock-count' => 'جرد المخزون',

        'payment-method-list' => 'عرض طرق الدفع',
        'payment-method-create' => 'إنشاء طريقة دفع',
        'payment-method-edit' => 'تعديل طريقة دفع',
        'payment-method-delete' => 'حذف طريقة دفع',

        'customer-list' => 'عرض العملاء',
        'customer-create' => 'إنشاء عميل',
        'customer-edit' => 'تعديل عميل',
        'customer-delete' => 'حذف عميل',
        'customer-show' => 'عرض تفاصيل العميل',

        'treasury-list' => 'عرض الخزائن',
        'treasury-create' => 'إنشاء خزينة',
        'treasury-edit' => 'تعديل خزينة',
        'treasury-delete' => 'حذف خزينة',

        'bank-account-list' => 'عرض الحسابات البنكية',
        'bank-account-create' => 'إنشاء حساب بنكي',
        'bank-account-edit' => 'تعديل حساب بنكي',
        'bank-account-delete' => 'حذف حساب بنكي',

        'financial-transfer-list' => 'عرض التحويلات المالية',
        'financial-transfer-create' => 'إنشاء تحويل مالي',

        'check-list' => 'عرض الشيكات',
        'check-create' => 'إنشاء شيك',
        'check-edit' => 'تعديل شيك',
        'check-show' => 'عرض تفاصيل الشيك',

        'cash-voucher-list' => 'عرض السندات النقدية',
        'cash-voucher-create' => 'إنشاء سند نقدي',
        'cash-voucher-show' => 'عرض/طباعة سند',

        'tax-list' => 'عرض الضرائب',
        'tax-create' => 'إنشاء ضريبة',
        'tax-edit' => 'تعديل ضريبة',
        'tax-delete' => 'حذف ضريبة',

        'bank-reconciliation-list' => 'عرض التسويات البنكية',
        'bank-reconciliation-create' => 'إنشاء تسوية بنكية',
        'bank-reconciliation-show' => 'عرض تسوية بنكية',

        'pos-access' => 'استخدام نقطة البيع',

        'sales-quote-list' => 'عرض عروض الأسعار',
        'sales-quote-create' => 'إنشاء عرض سعر',
        'sales-quote-show' => 'عرض عرض سعر',
        'sales-quote-convert' => 'تحويل عرض إلى فاتورة',

        'purchase-order-list' => 'عرض أوامر الشراء',
        'purchase-order-create' => 'إنشاء أمر شراء',
        'purchase-order-show' => 'عرض أمر شراء',
        'purchase-order-convert' => 'تحويل أمر إلى فاتورة',

        'product-batch-list' => 'عرض دفعات المنتجات',
        'product-batch-create' => 'إنشاء دفعة',
        'product-batch-edit' => 'تعديل دفعة',

        'fiscal-year-manage' => 'إدارة السنة المالية',

        'sale-invoice-list' => 'عرض فواتير البيع',
        'sale-invoice-create' => 'إنشاء فاتورة بيع',
        'sale-invoice-edit' => 'تعديل فاتورة بيع',
        'sale-invoice-delete' => 'حذف فاتورة بيع',
        'sale-invoice-show' => 'عرض تفاتورة بيع',
        'sale-invoice-confirm' => 'تأكيد فاتورة بيع',

        'sale-return-list' => 'عرض مرتجعات البيع',
        'sale-return-create' => 'إنشاء مرتجع بيع',
        'sale-return-show' => 'عرض مرتجع بيع',
        'sale-return-complete' => 'إكمال مرتجع بيع',

        'coupon-list' => 'عرض الكوبونات',
        'coupon-create' => 'إنشاء كوبون',
        'coupon-edit' => 'تعديل كوبون',
        'coupon-delete' => 'حذف كوبون',

        'supplier-list' => 'عرض الموردين',
        'supplier-create' => 'إنشاء مورد',
        'supplier-edit' => 'تعديل مورد',
        'supplier-delete' => 'حذف مورد',
        'supplier-show' => 'عرض تفاصيل المورد',

        'purchase-invoice-list' => 'عرض فواتير الشراء',
        'purchase-invoice-create' => 'إنشاء فاتورة شراء',
        'purchase-invoice-edit' => 'تعديل فاتورة شراء',
        'purchase-invoice-delete' => 'حذف فاتورة شراء',
        'purchase-invoice-show' => 'عرض فاتورة شراء',
        'purchase-invoice-confirm' => 'تأكيد فاتورة شراء',

        'purchase-return-list' => 'عرض مرتجعات الشراء',
        'purchase-return-create' => 'إنشاء مرتجع شراء',
        'purchase-return-show' => 'عرض مرتجع شراء',
        'purchase-return-complete' => 'إكمال مرتجع شراء',

        'reports-sales' => 'تقرير المبيعات',
        'reports-purchases' => 'تقرير المشتريات',
        'reports-profit' => 'تقرير الأرباح',
        'reports-inventory' => 'تقرير المخزون',
        'reports-partners' => 'تقرير الشركاء',
        'reports-taxes' => 'تقرير الضرائب',
        'reports-view' => 'عرض التقارير العامة',

        'promotion-list' => 'عرض العروض',
        'promotion-create' => 'إنشاء عرض',
        'promotion-edit' => 'تعديل عرض',
        'promotion-delete' => 'حذف عرض',

        'price-list-list' => 'عرض قوائم الأسعار',
        'price-list-create' => 'إنشاء قائمة أسعار',
        'price-list-edit' => 'تعديل قائمة أسعار',
        'price-list-delete' => 'حذف قائمة أسعار',

        'customer-segment-list' => 'عرض شرائح العملاء',
        'customer-segment-create' => 'إنشاء شريحة عملاء',
        'customer-segment-edit' => 'تعديل شريحة عملاء',
        'customer-segment-delete' => 'حذف شريحة عملاء',

        'loyalty-list' => 'عرض نقاط الولاء',
        'loyalty-adjust' => 'تعديل نقاط الولاء',

        'chart-of-account-list' => 'عرض دليل الحسابات',
        'chart-of-account-create' => 'إنشاء حساب',
        'chart-of-account-edit' => 'تعديل حساب',
        'chart-of-account-delete' => 'حذف حساب',
        'journal-entry-list' => 'عرض القيود اليومية',
        'journal-entry-show' => 'عرض تفاصيل القيد',
        'journal-entry-create' => 'إنشاء قيد يدوي',
        'journal-entry-post' => 'ترحيل قيد',
        'journal-entry-reverse' => 'عكس قيد',

        'attachment-list' => 'عرض المرفقات',
        'attachment-delete' => 'حذف مرفق',

        'discount_above_10' => 'خصم أكثر من 10%',
        'edit_confirmed_invoice' => 'تعديل فاتورة مؤكدة',
        'cancel_financial_transaction' => 'إلغاء معاملة مالية',
        'view_all_branches' => 'عرض جميع الفروع',
        'manage_audit_logs' => 'إدارة سجل التدقيق',

        'dashboard-view' => 'عرض لوحة التحكم',
        'settings-manage' => 'إدارة الإعدادات',
    ],

];
