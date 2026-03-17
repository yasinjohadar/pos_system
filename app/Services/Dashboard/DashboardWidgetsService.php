<?php

namespace App\Services\Dashboard;

use App\Models\Check;
use App\Models\Customer;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoice;
use App\Models\Supplier;
use App\Services\Reports\InventoryReportService;
use App\Services\Reports\ProductPerformanceService;
use App\Services\Reports\CustomerPerformanceService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardWidgetsService
{
    public function getTodaySales(): array
    {
        $today = Carbon::today()->toDateString();
        $q = SaleInvoice::where('status', 'confirmed')->whereDate('invoice_date', $today);
        return [
            'total' => (float) $q->sum('total'),
            'count' => $q->count(),
        ];
    }

    public function getTodayPurchases(): array
    {
        $today = Carbon::today()->toDateString();
        $q = PurchaseInvoice::where('status', 'confirmed')->whereDate('invoice_date', $today);
        return [
            'total' => (float) $q->sum('total'),
            'count' => $q->count(),
        ];
    }

    public function getCustomersBalance(): float
    {
        $ids = SaleInvoice::where('status', 'confirmed')->whereNotNull('customer_id')->distinct()->pluck('customer_id');
        return (float) Customer::whereIn('id', $ids)->get()->sum(fn ($c) => $c->balance);
    }

    public function getSuppliersBalance(): float
    {
        return (float) Supplier::all()->sum(fn ($s) => $s->balance);
    }

    public function getStockAlerts(): Collection
    {
        return app(InventoryReportService::class)->getReorderSuggestions();
    }

    public function getDueChecks(int $days = 7): Collection
    {
        $from = Carbon::today()->toDateString();
        $to = Carbon::today()->addDays($days)->toDateString();
        return Check::whereBetween('due_date', [$from, $to])
            ->where('status', Check::STATUS_UNDER_COLLECTION)
            ->orderBy('due_date')
            ->get();
    }

    public function getTopProducts(int $limit = 5): Collection
    {
        return app(ProductPerformanceService::class)->getTopProducts($limit);
    }

    public function getTopCustomers(int $limit = 5): Collection
    {
        return app(CustomerPerformanceService::class)->getTopCustomers($limit);
    }

    public function getAllWidgets(): array
    {
        return [
            'today_sales' => $this->getTodaySales(),
            'today_purchases' => $this->getTodayPurchases(),
            'customers_balance' => $this->getCustomersBalance(),
            'suppliers_balance' => $this->getSuppliersBalance(),
            'stock_alerts_count' => $this->getStockAlerts()->count(),
            'due_checks' => $this->getDueChecks(7),
            'top_products' => $this->getTopProducts(5),
            'top_customers' => $this->getTopCustomers(5),
        ];
    }
}
