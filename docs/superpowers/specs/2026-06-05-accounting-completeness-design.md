# Accounting Completeness Design — Phases 0 & 1

**Date:** 2026-06-05  
**Goal:** Transform ERP-lite into integrated accounting (POS + distribution + full accounting).

---

## Phase 0 — Integration Foundation

### 0.1 Company Settings (`group=company`)

| Key | Type | Description |
|-----|------|-------------|
| `company_name` | string | Legal / trade name |
| `company_logo` | string | Path to uploaded logo |
| `tax_number` | string | TRN / VAT registration |
| `default_currency` | string | e.g. SAR, USD |
| `invoice_footer` | text | Footer on printed invoices |
| `default_tax_id` | integer | Default Tax model FK |

**UI:** `/admin/settings/company` — single form, `settings-manage` permission.

### 0.2 Tax Management

CRUD for `Tax` model: name, type (`percent`|`fixed`), rate, is_active.

- Permissions: `tax-list`, `tax-create`, `tax-edit`, `tax-delete`
- Wire `tax_id` on Product create/edit forms
- Sale/Purchase invoice forms: optional tax picker that sets `tax_rate` from selected tax

### 0.3 Cash Vouchers Completion

Add columns: `status` (`active`|`cancelled`), `cancelled_at`, `cancelled_by`.

| Action | Route | Permission |
|--------|-------|------------|
| Show | `GET cash-vouchers/{voucher}` | `cash-voucher-show` |
| Print | `GET cash-vouchers/{voucher}/print` | `cash-voucher-show` |
| Cancel | `POST cash-vouchers/{voucher}/cancel` | `cancel_financial_transaction` |

Cancel creates reversal journal entry via `AccountingService::createReversalEntry()`.

### 0.4 Sidebar Restructure

New top-level **المحاسبة** section containing:
- إعدادات الشركة, الضرائب, سندات القبض/الصرف, السنوات المالية, شجرة الحسابات, القيود اليومية, ميزان المراجعة, قائمة الدخل, المرفقات

Remove accounting items from المبيعات submenu (keep treasuries/banks under Sales or move to المحاسبة — treasuries stay under Sales for operational flow).

### 0.5 Advanced Permissions

- `edit_confirmed_invoice`: allow edit/update on confirmed sale/purchase invoices
- `cancel_financial_transaction`: required for voucher cancel

---

## Phase 1 — Full Accounting

### 1.1 Manual Journal Entries

Extend `JournalEntry`:
- `source` enum: `auto`, `manual`, `closing`, `reversal`
- `reversed_entry_id` nullable FK
- `fiscal_year_id` nullable

| Action | Permission |
|--------|------------|
| Create/store draft | `journal-entry-create` |
| Post | `journal-entry-post` |
| Reverse | `journal-entry-reverse` |

Validation: sum(debit) === sum(credit), min 2 lines, accounts active, fiscal period not closed.

### 1.2 General Ledger & Account Statement

- **GL:** `/admin/reports/general-ledger` — all posted lines by account, date range
- **Account statement:** `/admin/reports/account-statement?account_id=` — running balance

### 1.3 Balance Sheet

`/admin/reports/balance-sheet` — aggregate posted balances by account type (asset, liability, equity) as of date.

### 1.4 Cash Flow Statement

`/admin/reports/cash-flow` — simplified indirect method from cash account (1100) movements.

### 1.5 Period Closing

Enhanced `FiscalYearController::close()`:
- Block if already closed
- Create closing entries (revenue/expense → retained earnings placeholder account 3200)
- Set `is_closed=true`, `is_active=false`
- `ClosedPeriodGuard` middleware/service blocks create/update on closed fiscal years

### 1.6 Bank Reconciliation (Simplified)

Model `BankReconciliation`: bank_account_id, statement_date, statement_balance, reconciled_at, notes.

Lines: `BankReconciliationItem` linking journal lines or manual adjustments.

UI: create reconciliation, mark items cleared, show difference.

---

## Phase 2 — POS (Summary)

- `PosShift` model: open/close, opening/closing cash, user, treasury
- `/admin/pos` fullscreen RTL cashier UI
- Barcode search, cart, multi-payment, hold/resume
- Creates `SaleInvoice` + stock + accounting on checkout
- Thermal receipt print view

---

## Phase 3 — Advanced Distribution (Summary)

- `SalesQuote` → convert to `SaleInvoice`
- `PurchaseOrder` → convert to `PurchaseInvoice`
- `ProductBatch` CRUD UI
- E-invoicing: export XML/QR on invoice print when company tax_number set

---

## Success Criteria

- Phase 0: Company settings saved; taxes CRUD; voucher show/print/cancel with reversal JE
- Phase 1: Manual JE post/reverse; GL + balance sheet + cash flow reports; fiscal close blocks edits
