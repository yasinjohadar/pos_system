<?php

return [
    'system_actor_label' => 'النظام',

    'actions' => [
        'create' => 'إنشاء',
        'update' => 'تعديل',
        'delete' => 'حذف',
        'confirm' => 'تأكيد',
        'cancel' => 'إلغاء',
    ],

    'action_verbs' => [
        'create' => 'أنشأ',
        'update' => 'عدّل',
        'delete' => 'حذف',
        'confirm' => 'أكّد',
        'cancel' => 'ألغى',
    ],

    'hidden_fields' => [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'password',
        'remember_token',
        'email_verified_at',
    ],

    'value_maps' => [
        'status' => [
            'draft' => 'مسودة',
            'confirmed' => 'مؤكدة',
            'cancelled' => 'ملغاة',
        ],
        'payment_status' => [
            'pending' => 'معلق',
            'partial' => 'جزئي',
            'paid' => 'مدفوع',
        ],
        'type' => [
            'receipt' => 'سند قبض',
            'payment' => 'سند صرف',
            'in' => 'إدخال',
            'out' => 'صرف',
            'transfer_in' => 'تحويل وارد',
            'transfer_out' => 'تحويل صادر',
            'adjustment' => 'تسوية',
            'inventory_count' => 'جرد',
            'return_sale' => 'مرتجع بيع',
            'return_purchase' => 'مرتجع شراء',
        ],
    ],

    'models' => [
        \App\Models\SaleInvoice::class => [
            'label' => 'فاتورة بيع',
            'route' => 'admin.sale-invoices.show',
            'summary_keys' => ['number', 'total', 'status'],
            'fields' => [
                'number' => 'الرقم',
                'invoice_date' => 'التاريخ',
                'branch_id' => 'الفرع',
                'customer_id' => 'العميل',
                'warehouse_id' => 'المخزن',
                'total' => 'الإجمالي',
                'status' => 'الحالة',
                'payment_status' => 'حالة الدفع',
                'notes' => 'ملاحظات',
            ],
        ],
        \App\Models\PurchaseInvoice::class => [
            'label' => 'فاتورة شراء',
            'route' => 'admin.purchase-invoices.show',
            'summary_keys' => ['number', 'total', 'status'],
            'fields' => [
                'number' => 'الرقم',
                'invoice_date' => 'التاريخ',
                'branch_id' => 'الفرع',
                'supplier_id' => 'المورد',
                'warehouse_id' => 'المخزن',
                'total' => 'الإجمالي',
                'status' => 'الحالة',
                'payment_status' => 'حالة الدفع',
                'notes' => 'ملاحظات',
            ],
        ],
        \App\Models\StockMovement::class => [
            'label' => 'حركة مخزون',
            'route' => null,
            'summary_keys' => ['type', 'quantity', 'product_id'],
            'fields' => [
                'type' => 'نوع الحركة',
                'product_id' => 'المنتج',
                'warehouse_id' => 'المخزن',
                'quantity' => 'الكمية',
                'movement_date' => 'التاريخ',
                'notes' => 'ملاحظات',
            ],
        ],
        \App\Models\CashVoucher::class => [
            'label' => 'سند نقدي',
            'route' => null,
            'summary_keys' => ['voucher_number', 'amount', 'type'],
            'fields' => [
                'voucher_number' => 'رقم السند',
                'type' => 'النوع',
                'date' => 'التاريخ',
                'amount' => 'المبلغ',
                'treasury_id' => 'الخزينة',
                'bank_account_id' => 'الحساب البنكي',
                'description' => 'الوصف',
                'notes' => 'ملاحظات',
            ],
        ],
    ],
];
