# Phase 0 Integration Foundation Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development or superpowers:executing-plans to implement task-by-task.

**Goal:** Company settings, tax CRUD, complete cash vouchers, accounting sidebar, permission enforcement.

**Architecture:** SystemSetting group `company` via CompanySettingsService; Tax resource controller; CashVoucher status + reversal in AccountingService; sidebar refactor.

**Tech Stack:** Laravel 12, Blade Premium UI, Spatie permissions, existing AccountingService events.

---

### Task 1: Migrations & Models

**Files:**
- Create: `database/migrations/2026_06_05_100000_add_status_to_cash_vouchers.php`
- Modify: `app/Models/CashVoucher.php`

- [ ] Add status, cancelled_at, cancelled_by to cash_vouchers
- [ ] Add constants STATUS_ACTIVE, STATUS_CANCELLED

### Task 2: Company Settings

**Files:**
- Create: `app/Services/Settings/CompanySettingsService.php`
- Create: `app/Http/Controllers/Admin/CompanySettingsController.php`
- Create: `resources/views/admin/pages/settings/company/index.blade.php`

- [ ] Service get/update with defaults
- [ ] Controller index/update with logo upload
- [ ] Routes under `settings/company`

### Task 3: Tax CRUD

**Files:**
- Create: `app/Http/Controllers/Admin/TaxController.php`
- Create: views under `resources/views/admin/pages/taxes/`
- Modify: `config/permissions.php`, `PermissionSeeder.php`
- Modify: product form partial for tax_id

### Task 4: Cash Voucher show/print/cancel

**Files:**
- Modify: `CashVoucherController.php`, `AccountingService.php`
- Create: show.blade.php, print.blade.php
- Modify: table-rows partial with actions

### Task 5: Permissions & Sidebar

**Files:**
- Modify: `SaleInvoiceController`, `PurchaseInvoiceController` edit guards
- Modify: `main-sidebar.blade.php`
- Modify: `routes/admin.php`

### Task 6: Tests

**Files:**
- Create: `tests/Feature/Phase0AccountingTest.php`

- [ ] Company settings save
- [ ] Tax CRUD
- [ ] Voucher cancel creates reversal
