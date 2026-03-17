<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\WhatsAppMessageReceived;
use App\Events\SaleInvoiceConfirmed;
use App\Events\PurchaseInvoiceConfirmed;
use App\Events\CashVoucherCreated;
use App\Listeners\AutoReplyWhatsAppListener;
use App\Listeners\CreateJournalEntryForSaleInvoice;
use App\Listeners\CreateJournalEntryForPurchaseInvoice;
use App\Listeners\CreateJournalEntryForVoucher;
use App\Models\SaleInvoice;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use App\Models\CashVoucher;
use App\Observers\SaleInvoiceObserver;
use App\Observers\PurchaseInvoiceObserver;
use App\Observers\StockMovementObserver;
use App\Observers\CashVoucherObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        SaleInvoice::observe(SaleInvoiceObserver::class);
        PurchaseInvoice::observe(PurchaseInvoiceObserver::class);
        StockMovement::observe(StockMovementObserver::class);
        CashVoucher::observe(CashVoucherObserver::class);

        // تسجيل PermissionServiceProvider
        $this->app->register(PermissionServiceProvider::class);

        // Register WhatsApp auto-reply listener
        Event::listen(
            WhatsAppMessageReceived::class,
            AutoReplyWhatsAppListener::class
        );

        // قيود يومية تلقائية
        Event::listen(SaleInvoiceConfirmed::class, CreateJournalEntryForSaleInvoice::class);
        Event::listen(PurchaseInvoiceConfirmed::class, CreateJournalEntryForPurchaseInvoice::class);
        Event::listen(CashVoucherCreated::class, CreateJournalEntryForVoucher::class);
    }
}