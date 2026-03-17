<?php

namespace App\Console\Commands;

use App\Mail\DailySummaryMail;
use App\Models\SaleInvoice;
use App\Models\PurchaseInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendDailySummaryCommand extends Command
{
    protected $signature = 'reports:daily-summary';

    protected $description = 'ملخص يومي: مبيعات ومشتريات اليوم';

    public function handle(): int
    {
        $today = now()->toDateString();
        $salesTotal = SaleInvoice::where('status', 'confirmed')->whereDate('invoice_date', $today)->sum('total');
        $salesCount = SaleInvoice::where('status', 'confirmed')->whereDate('invoice_date', $today)->count();
        $purchasesTotal = PurchaseInvoice::where('status', 'confirmed')->whereDate('invoice_date', $today)->sum('total');
        $purchasesCount = PurchaseInvoice::where('status', 'confirmed')->whereDate('invoice_date', $today)->count();

        $msg = "ملخص يوم $today: مبيعات: $salesCount فاتورة، " . number_format($salesTotal, 2) . " | مشتريات: $purchasesCount فاتورة، " . number_format($purchasesTotal, 2);
        $this->info($msg);
        Log::channel('single')->info('Daily summary', ['date' => $today, 'sales_count' => $salesCount, 'sales_total' => $salesTotal, 'purchases_count' => $purchasesCount, 'purchases_total' => $purchasesTotal]);

        $recipient = config('mail.summary_recipient') ?: env('SUMMARY_RECIPIENT_EMAIL');
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new DailySummaryMail($today, $salesCount, (float) $salesTotal, $purchasesCount, (float) $purchasesTotal));
                $this->info('تم إرسال الملخص بالبريد إلى: ' . $recipient);
            } catch (\Throwable $e) {
                $this->warn('فشل إرسال البريد: ' . $e->getMessage());
                Log::channel('single')->warning('Daily summary email failed', ['error' => $e->getMessage()]);
            }
        }

        return Command::SUCCESS;
    }
}
