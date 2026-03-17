<?php

namespace App\Services\Accounting;

use App\Models\CashVoucher;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoice;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /** أكواد الحسابات الافتراضية (يجب أن تكون موجودة في شجرة الحسابات) */
    public const CODE_CASH = '1100';
    public const CODE_CUSTOMERS = '1200';
    public const CODE_SUPPLIERS = '2100';
    public const CODE_TAX = '2200';
    public const CODE_SALES = '4100';
    public const CODE_PURCHASES = '5100';
    public const CODE_EXPENSES = '5200';

    /**
     * قيد فاتورة بيع: من ح/العملاء إلى ح/المبيعات + ح/ضريبة المبيعات
     */
    public function createSaleInvoiceEntry(SaleInvoice $invoice): ?JournalEntry
    {
        $customers = ChartOfAccount::findByCode(self::CODE_CUSTOMERS);
        $sales = ChartOfAccount::findByCode(self::CODE_SALES);
        $tax = ChartOfAccount::findByCode(self::CODE_TAX);
        if (!$customers || !$sales) {
            return null;
        }

        $total = (float) $invoice->total;
        $taxAmount = (float) $invoice->tax_amount;
        $netSales = $total - $taxAmount;

        return DB::transaction(function () use ($invoice, $customers, $sales, $tax, $total, $taxAmount, $netSales) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $invoice->invoice_date,
                'description' => 'قيد فاتورة مبيعات #' . $invoice->number,
                'reference_type' => SaleInvoice::class,
                'reference_id' => $invoice->id,
                'is_posted' => true,
                'created_by' => $invoice->user_id ?? auth()->id(),
            ]);

            $this->addLine($entry->id, $customers->id, $total, 0, 'مدين - عميل');
            $this->addLine($entry->id, $sales->id, 0, $netSales, 'مبيعات');
            if ($tax && $taxAmount > 0) {
                $this->addLine($entry->id, $tax->id, 0, $taxAmount, 'ضريبة مبيعات');
            }

            return $entry->load('lines.account');
        });
    }

    /**
     * قيد فاتورة شراء: من ح/المشتريات + ح/الضريبة إلى ح/الموردين
     */
    public function createPurchaseInvoiceEntry(PurchaseInvoice $invoice): ?JournalEntry
    {
        $suppliers = ChartOfAccount::findByCode(self::CODE_SUPPLIERS);
        $purchases = ChartOfAccount::findByCode(self::CODE_PURCHASES);
        $tax = ChartOfAccount::findByCode(self::CODE_TAX);
        if (!$suppliers || !$purchases) {
            return null;
        }

        $total = (float) $invoice->total;
        $taxAmount = (float) $invoice->tax_amount;
        $netPurchases = $total - $taxAmount;

        return DB::transaction(function () use ($invoice, $suppliers, $purchases, $tax, $total, $taxAmount, $netPurchases) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $invoice->invoice_date,
                'description' => 'قيد فاتورة مشتريات #' . $invoice->number,
                'reference_type' => PurchaseInvoice::class,
                'reference_id' => $invoice->id,
                'is_posted' => true,
                'created_by' => $invoice->user_id ?? auth()->id(),
            ]);

            $this->addLine($entry->id, $purchases->id, $netPurchases, 0, 'مشتريات');
            if ($tax && $taxAmount > 0) {
                $this->addLine($entry->id, $tax->id, $taxAmount, 0, 'ضريبة مشتريات');
            }
            $this->addLine($entry->id, $suppliers->id, 0, $total, 'دائن - مورد');

            return $entry->load('lines.account');
        });
    }

    /**
     * قيد سند قبض: من ح/الصندوق إلى ح/العملاء
     */
    public function createReceiptVoucherEntry(CashVoucher $voucher): ?JournalEntry
    {
        if ($voucher->type !== CashVoucher::TYPE_RECEIPT) {
            return null;
        }
        $cash = ChartOfAccount::findByCode(self::CODE_CASH);
        $customers = ChartOfAccount::findByCode(self::CODE_CUSTOMERS);
        if (!$cash || !$customers) {
            return null;
        }
        $amount = (float) $voucher->amount;

        return DB::transaction(function () use ($voucher, $cash, $customers, $amount) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $voucher->date,
                'description' => 'سند قبض #' . $voucher->voucher_number . ' - ' . ($voucher->description ?? ''),
                'reference_type' => CashVoucher::class,
                'reference_id' => $voucher->id,
                'is_posted' => true,
                'created_by' => $voucher->user_id ?? auth()->id(),
            ]);
            $this->addLine($entry->id, $cash->id, $amount, 0, 'قبض');
            $this->addLine($entry->id, $customers->id, 0, $amount, 'عملاء');
            return $entry->load('lines.account');
        });
    }

    /**
     * قيد سند صرف: من ح/المصروفات إلى ح/الصندوق
     */
    public function createPaymentVoucherEntry(CashVoucher $voucher): ?JournalEntry
    {
        if ($voucher->type !== CashVoucher::TYPE_PAYMENT) {
            return null;
        }
        $cash = ChartOfAccount::findByCode(self::CODE_CASH);
        $expenses = ChartOfAccount::findByCode(self::CODE_EXPENSES);
        if (!$cash || !$expenses) {
            return null;
        }
        $amount = (float) $voucher->amount;

        return DB::transaction(function () use ($voucher, $cash, $expenses, $amount) {
            $entry = JournalEntry::create([
                'entry_number' => JournalEntry::generateEntryNumber(),
                'entry_date' => $voucher->date,
                'description' => 'سند صرف #' . $voucher->voucher_number . ' - ' . ($voucher->description ?? ''),
                'reference_type' => CashVoucher::class,
                'reference_id' => $voucher->id,
                'is_posted' => true,
                'created_by' => $voucher->user_id ?? auth()->id(),
            ]);
            $this->addLine($entry->id, $expenses->id, $amount, 0, 'مصروف');
            $this->addLine($entry->id, $cash->id, 0, $amount, 'صرف');
            return $entry->load('lines.account');
        });
    }

    private function addLine(int $journalEntryId, int $accountId, float $debit, float $credit, ?string $description = null): JournalEntryLine
    {
        return JournalEntryLine::create([
            'journal_entry_id' => $journalEntryId,
            'account_id' => $accountId,
            'debit' => $debit,
            'credit' => $credit,
            'description' => $description,
        ]);
    }
}
