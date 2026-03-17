<?php

namespace App\Console\Commands;

use App\Mail\WeeklySummaryMail;
use App\Models\SaleInvoice;
use App\Models\PurchaseInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklySummaryCommand extends Command
{
    protected $signature = 'reports:weekly-summary';

    protected $description = 'ملخص أسبوعي: مبيعات ومشتريات الأسبوع الماضي';

    public function handle(): int
    {
        $start = now()->subWeek()->startOfWeek()->toDateString();
        $end = now()->subWeek()->endOfWeek()->toDateString();
        $salesTotal = SaleInvoice::where('status', 'confirmed')->whereBetween('invoice_date', [$start, $end])->sum('total');
        $salesCount = SaleInvoice::where('status', 'confirmed')->whereBetween('invoice_date', [$start, $end])->count();
        $purchasesTotal = PurchaseInvoice::where('status', 'confirmed')->whereBetween('invoice_date', [$start, $end])->sum('total');
        $purchasesCount = PurchaseInvoice::where('status', 'confirmed')->whereBetween('invoice_date', [$start, $end])->count();

        $msg = "ملخص أسبوعي ($start إلى $end): مبيعات: $salesCount فاتورة، " . number_format($salesTotal, 2) . " | مشتريات: $purchasesCount فاتورة، " . number_format($purchasesTotal, 2);
        $this->info($msg);
        Log::channel('single')->info('Weekly summary', ['start' => $start, 'end' => $end, 'sales_count' => $salesCount, 'sales_total' => $salesTotal, 'purchases_count' => $purchasesCount, 'purchases_total' => $purchasesTotal]);

        $recipient = config('mail.summary_recipient') ?: env('SUMMARY_RECIPIENT_EMAIL');
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new WeeklySummaryMail($start, $end, $salesCount, (float) $salesTotal, $purchasesCount, (float) $purchasesTotal));
                $this->info('تم إرسال الملخص بالبريد إلى: ' . $recipient);
            } catch (\Throwable $e) {
                $this->warn('فشل إرسال البريد: ' . $e->getMessage());
                Log::channel('single')->warning('Weekly summary email failed', ['error' => $e->getMessage()]);
            }
        }

        return Command::SUCCESS;
    }
}
