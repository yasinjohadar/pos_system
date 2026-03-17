<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // صلاحيات الأدوار
            "role-list",
            "role-create",
            "role-edit",
            "role-delete",

            // صلاحيات المستخدمين
            "user-list",
            "user-create",
            "user-edit",
            "user-delete",
            "user-show",

            // صلاحيات الفروع
            "branch-list",
            "branch-create",
            "branch-edit",
            "branch-delete",
            "branch-show",

            // صلاحيات المخازن
            "warehouse-list",
            "warehouse-create",
            "warehouse-edit",
            "warehouse-delete",
            "warehouse-show",

            // صلاحيات التصنيفات
            "category-list",
            "category-create",
            "category-edit",
            "category-delete",
            "category-show",

            // صلاحيات المنتجات
            "product-list",
            "product-create",
            "product-edit",
            "product-delete",
            "product-show",
            "product-featured",

            // صلاحيات الوحدات
            "unit-list",
            "unit-create",
            "unit-edit",
            "unit-delete",
            "unit-show",

            // إدارة المخزون
            "stock-list",
            "stock-movement-create",
            "stock-transfer-create",
            "stock-count",

            // المبيعات
            "payment-method-list",
            "payment-method-create",
            "payment-method-edit",
            "payment-method-delete",
            "customer-list",
            "customer-create",
            "customer-edit",
            "customer-delete",
            "customer-show",
            "treasury-list",
            "treasury-create",
            "treasury-edit",
            "treasury-delete",
            "bank-account-list",
            "bank-account-create",
            "bank-account-edit",
            "bank-account-delete",
            "financial-transfer-list",
            "financial-transfer-create",
            "check-list",
            "check-create",
            "check-edit",
            "check-show",
            "cash-voucher-list",
            "cash-voucher-create",
            "fiscal-year-manage",
            "reports-sales",
            "reports-purchases",
            "reports-profit",
            "reports-inventory",
            "reports-partners",
            "reports-taxes",
            "sale-invoice-list",
            "sale-invoice-create",
            "sale-invoice-edit",
            "sale-invoice-delete",
            "sale-invoice-show",
            "sale-invoice-confirm",
            "sale-return-list",
            "sale-return-create",
            "sale-return-show",
            "sale-return-complete",
            "coupon-list",
            "coupon-create",
            "coupon-edit",
            "coupon-delete",

            // المشتريات
            "supplier-list",
            "supplier-create",
            "supplier-edit",
            "supplier-delete",
            "supplier-show",
            "purchase-invoice-list",
            "purchase-invoice-create",
            "purchase-invoice-edit",
            "purchase-invoice-delete",
            "purchase-invoice-show",
            "purchase-invoice-confirm",
            "purchase-return-list",
            "purchase-return-create",
            "purchase-return-show",
            "purchase-return-complete",

            // صلاحيات إضافية للنظام
            "dashboard-view",
            "settings-manage",
            "reports-view",

            // العروض وقوائم الأسعار
            "promotion-list",
            "promotion-create",
            "promotion-edit",
            "promotion-delete",
            "price-list-list",
            "price-list-create",
            "price-list-edit",
            "price-list-delete",

            // شرائح العملاء ونقاط الولاء
            "customer-segment-list",
            "customer-segment-create",
            "customer-segment-edit",
            "customer-segment-delete",
            "loyalty-list",
            "loyalty-adjust",

            // المرحلة 4 - صلاحيات إضافية وسجل التدقيق
            "discount_above_10",
            "edit_confirmed_invoice",
            "cancel_financial_transaction",
            "view_all_branches",
            "manage_audit_logs",
            "chart-of-account-list",
            "chart-of-account-create",
            "chart-of-account-edit",
            "chart-of-account-delete",
            "journal-entry-list",
            "journal-entry-show",
            "attachment-list",
            "attachment-delete",
        ];

        foreach ($permissions as $key => $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
