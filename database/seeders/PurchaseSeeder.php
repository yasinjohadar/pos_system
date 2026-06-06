<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Treasury;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();
        $branches = Branch::where('is_active', true)->orderBy('id')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('id')->get();
        $products = Product::where('is_active', true)->orderBy('id')->limit(30)->get();
        $cashMethod = PaymentMethod::where('code', PaymentMethod::CODE_CASH)->first();
        $treasury = Treasury::where('is_active', true)->first();

        if (! $user || $branches->isEmpty() || $warehouses->isEmpty() || $products->isEmpty()) {
            $this->command?->warn('PurchaseSeeder: يتطلب مستخدماً وفروعاً ومخازن ومنتجات.');

            return;
        }

        $suppliers = $this->seedSuppliers();

        if (PurchaseInvoice::exists()) {
            $this->command?->info('PurchaseSeeder: فواتير موجودة — تخطي إنشاء الفواتير.');
            $this->seedReturns($user);
            $this->seedReportSnapshots($user, $branches, $warehouses, $products, $suppliers);
            $this->seedAgingSnapshots($user, $branches, $warehouses, $products);

            return;
        }

        DB::transaction(function () use ($user, $branches, $warehouses, $products, $cashMethod, $treasury, $suppliers) {
            $scenarios = [
                ['status' => 'draft', 'payment' => 'pending', 'days' => 0, 'tax' => 0],
                ['status' => 'draft', 'payment' => 'pending', 'days' => 1, 'tax' => 15],
                ['status' => 'draft', 'payment' => 'pending', 'days' => 2, 'tax' => 0],
                ['status' => 'confirmed', 'payment' => 'paid', 'days' => 3, 'tax' => 15],
                ['status' => 'confirmed', 'payment' => 'paid', 'days' => 5, 'tax' => 15],
                ['status' => 'confirmed', 'payment' => 'paid', 'days' => 7, 'tax' => 0],
                ['status' => 'confirmed', 'payment' => 'partial', 'days' => 10, 'tax' => 15],
                ['status' => 'confirmed', 'payment' => 'partial', 'days' => 12, 'tax' => 15],
                ['status' => 'confirmed', 'payment' => 'pending', 'days' => 14, 'tax' => 15],
                ['status' => 'confirmed', 'payment' => 'pending', 'days' => 18, 'tax' => 0],
                ['status' => 'confirmed', 'payment' => 'paid', 'days' => 20, 'tax' => 15],
                ['status' => 'confirmed', 'payment' => 'paid', 'days' => 25, 'tax' => 15],
            ];

            foreach ($scenarios as $index => $scenario) {
                $branch = $branches[$index % $branches->count()];
                $warehouse = $warehouses->firstWhere('branch_id', $branch->id) ?? $warehouses[$index % $warehouses->count()];
                $supplier = $suppliers[$index % $suppliers->count()];
                $invoiceDate = Carbon::today()->subDays($scenario['days']);

                $invoice = PurchaseInvoice::create([
                    'number' => 'PUR-DEMO-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'invoice_date' => $invoiceDate,
                    'branch_id' => $branch->id,
                    'supplier_id' => $supplier->id,
                    'warehouse_id' => $warehouse->id,
                    'tax_rate' => $scenario['tax'],
                    'discount_type' => 'fixed',
                    'discount_value' => 0,
                    'status' => PurchaseInvoice::STATUS_DRAFT,
                    'payment_status' => PurchaseInvoice::PAYMENT_STATUS_PENDING,
                    'user_id' => $user->id,
                    'notes' => 'فاتورة شراء تجريبية #' . ($index + 1),
                ]);

                $itemCount = random_int(1, 3);
                $picked = $products->random(min($itemCount, $products->count()));

                foreach ($picked as $product) {
                    $qty = random_int(5, 20);
                    $unitPrice = (float) ($product->cost_price ?: $product->base_price ?: 10);
                    PurchaseInvoiceItem::create([
                        'purchase_invoice_id' => $invoice->id,
                        'product_id' => $product->id,
                        'warehouse_id' => $warehouse->id,
                        'quantity' => $qty,
                        'unit_price' => $unitPrice,
                        'total' => round($qty * $unitPrice, 2),
                    ]);
                }

                $invoice->recalculateTotals();

                if ($scenario['status'] === 'confirmed') {
                    try {
                        $invoice->confirm();
                    } catch (\Throwable $e) {
                        $invoice->update(['status' => PurchaseInvoice::STATUS_DRAFT]);
                        $this->command?->warn("PurchaseSeeder: تعذر اعتماد فاتورة {$invoice->number}: {$e->getMessage()}");
                        continue;
                    }

                    if ($cashMethod && $treasury && in_array($scenario['payment'], ['paid', 'partial'], true)) {
                        $amount = $scenario['payment'] === 'paid'
                            ? (float) $invoice->total
                            : round((float) $invoice->total * 0.5, 2);

                        SupplierPayment::create([
                            'supplier_id' => $supplier->id,
                            'amount' => $amount,
                            'payment_method_id' => $cashMethod->id,
                            'treasury_id' => $treasury->id,
                            'payment_date' => $invoiceDate,
                            'purchase_invoice_id' => $invoice->id,
                            'user_id' => $user->id,
                            'notes' => 'دفعة تجريبية',
                        ]);
                    }
                }
            }

            $this->seedReturns($user);
            $this->seedReportSnapshots($user, $branches, $warehouses, $products, $suppliers);
            $this->seedAgingSnapshots($user, $branches, $warehouses, $products);
        });

        $this->command?->info('PurchaseSeeder: تم إنشاء بيانات المشتريات التجريبية.');
    }

    private function seedReturns(User $user): void
    {
        if (PurchaseReturn::exists()) {
            return;
        }

        $invoices = PurchaseInvoice::where('status', PurchaseInvoice::STATUS_CONFIRMED)
            ->with('items')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        foreach ($invoices as $index => $invoice) {
            if ($invoice->items->isEmpty()) {
                continue;
            }

            $item = $invoice->items->first();
            $returnQty = min(1, (float) $item->quantity);
            $refund = round($returnQty * (float) $item->unit_price, 2);
            $taxRefund = round($refund * (float) $invoice->tax_rate / 100, 2);

            $purchaseReturn = PurchaseReturn::create([
                'return_number' => 'PRET-DEMO-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'purchase_invoice_id' => $invoice->id,
                'return_date' => $invoice->invoice_date->copy()->addDay(),
                'warehouse_id' => $invoice->warehouse_id,
                'subtotal_refund' => $refund,
                'tax_refund' => $taxRefund,
                'total_refund' => $refund + $taxRefund,
                'status' => $index === 0 ? PurchaseReturn::STATUS_PENDING : PurchaseReturn::STATUS_COMPLETED,
                'user_id' => $user->id,
                'notes' => 'مرتجع شراء تجريبي',
            ]);

            PurchaseReturnItem::create([
                'purchase_return_id' => $purchaseReturn->id,
                'purchase_invoice_item_id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $returnQty,
                'unit_price' => $item->unit_price,
                'total' => $refund,
            ]);

            if ($purchaseReturn->status === PurchaseReturn::STATUS_COMPLETED) {
                try {
                    $purchaseReturn->complete();
                } catch (\Throwable $e) {
                    $purchaseReturn->update(['status' => PurchaseReturn::STATUS_PENDING]);
                    $this->command?->warn("PurchaseSeeder: تعذر إكمال مرتجع {$purchaseReturn->return_number}: {$e->getMessage()}");
                }
            }
        }
    }

    private function seedSuppliers(): \Illuminate\Support\Collection
    {
        $names = [
            ['name' => 'شركة التوريدات المتحدة', 'phone' => '0112345678', 'email' => 'supply@demo.com'],
            ['name' => 'مؤسسة الأجهزة الحديثة', 'phone' => '0123456789', 'email' => 'devices@demo.com'],
            ['name' => 'شركة المواد الغذائية', 'phone' => '0134567890', 'email' => 'food@demo.com'],
            ['name' => 'مورد الإلكترونيات', 'phone' => '0145678901', 'email' => 'electronics@demo.com'],
            ['name' => 'شركة التعبئة والتغليف', 'phone' => '0156789012', 'email' => 'packaging@demo.com'],
            ['name' => 'مؤسسة البناء والتشييد', 'phone' => '0167890123', 'email' => 'build@demo.com'],
        ];

        return collect($names)->map(fn ($row) => Supplier::firstOrCreate(
            ['phone' => $row['phone']],
            [
                'name' => $row['name'],
                'email' => $row['email'],
                'address' => 'الرياض — المنطقة الصناعية',
                'opening_balance' => 0,
                'is_active' => true,
            ]
        ));
    }

    /**
     * فواتير بتواريخ اليوم/أمس لمعاينة تقارير المشتريات.
     */
    private function seedReportSnapshots(
        User $user,
        \Illuminate\Support\Collection $branches,
        \Illuminate\Support\Collection $warehouses,
        \Illuminate\Support\Collection $products,
        \Illuminate\Support\Collection $suppliers
    ): void {
        $today = Carbon::today();
        $scenarios = [
            ['number' => 'PUR-REPORT-TODAY-01', 'date' => $today, 'tax' => 15, 'item_count' => 2],
            ['number' => 'PUR-REPORT-TODAY-02', 'date' => $today, 'tax' => 0, 'item_count' => 1],
            ['number' => 'PUR-REPORT-YDAY-01', 'date' => $today->copy()->subDay(), 'tax' => 15, 'item_count' => 2],
        ];

        foreach ($scenarios as $index => $scenario) {
            if (PurchaseInvoice::where('number', $scenario['number'])->exists()) {
                continue;
            }

            $branch = $branches[$index % $branches->count()];
            $warehouse = $warehouses->firstWhere('branch_id', $branch->id) ?? $warehouses->first();
            $supplier = $suppliers[$index % $suppliers->count()];

            $invoice = PurchaseInvoice::create([
                'number' => $scenario['number'],
                'invoice_date' => $scenario['date'],
                'branch_id' => $branch->id,
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'tax_rate' => $scenario['tax'],
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'status' => PurchaseInvoice::STATUS_DRAFT,
                'payment_status' => PurchaseInvoice::PAYMENT_STATUS_PENDING,
                'user_id' => $user->id,
                'notes' => 'فاتورة تجريبية لتقرير المشتريات اليومي',
            ]);

            $picked = $products->random(min($scenario['item_count'], $products->count()));
            foreach ($picked as $product) {
                $qty = random_int(5, 15);
                $unitPrice = (float) ($product->cost_price ?: $product->base_price ?: 10);
                PurchaseInvoiceItem::create([
                    'purchase_invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => round($qty * $unitPrice, 2),
                ]);
            }

            $invoice->recalculateTotals();

            try {
                $invoice->confirm();
            } catch (\Throwable $e) {
                $invoice->delete();
                $this->command?->warn("PurchaseSeeder: تعذر إنشاء فاتورة تقرير {$scenario['number']}: {$e->getMessage()}");
            }
        }

        if (PurchaseReturn::where('return_number', 'PRET-REPORT-TODAY-01')->exists()) {
            return;
        }

        $sourceInvoice = PurchaseInvoice::where('number', 'PUR-REPORT-TODAY-02')
            ->where('status', PurchaseInvoice::STATUS_CONFIRMED)
            ->with('items')
            ->first();

        if (! $sourceInvoice || $sourceInvoice->items->isEmpty()) {
            return;
        }

        $item = $sourceInvoice->items->first();
        $returnQty = min(1, (float) $item->quantity);
        $refund = round($returnQty * (float) $item->unit_price, 2);
        $taxRefund = round($refund * (float) $sourceInvoice->tax_rate / 100, 2);

        $purchaseReturn = PurchaseReturn::create([
            'return_number' => 'PRET-REPORT-TODAY-01',
            'purchase_invoice_id' => $sourceInvoice->id,
            'return_date' => $today,
            'warehouse_id' => $sourceInvoice->warehouse_id,
            'subtotal_refund' => $refund,
            'tax_refund' => $taxRefund,
            'total_refund' => $refund + $taxRefund,
            'status' => PurchaseReturn::STATUS_PENDING,
            'user_id' => $user->id,
            'notes' => 'مرتجع تجريبي لتقرير المشتريات اليومي',
        ]);

        PurchaseReturnItem::create([
            'purchase_return_id' => $purchaseReturn->id,
            'purchase_invoice_item_id' => $item->id,
            'product_id' => $item->product_id,
            'quantity' => $returnQty,
            'unit_price' => $item->unit_price,
            'total' => $refund,
        ]);

        try {
            $purchaseReturn->complete();
        } catch (\Throwable $e) {
            $purchaseReturn->update(['status' => PurchaseReturn::STATUS_PENDING]);
            $this->command?->warn("PurchaseSeeder: تعذر إكمال مرتجع التقرير: {$e->getMessage()}");
        }
    }

    /**
     * فواتير شراء آجلة بتواريخ متفرقة لمعاينة تقرير أعمار ذمم الموردين.
     */
    private function seedAgingSnapshots(
        User $user,
        \Illuminate\Support\Collection $branches,
        \Illuminate\Support\Collection $warehouses,
        \Illuminate\Support\Collection $products
    ): void {
        if (PurchaseInvoice::where('number', 'PUR-AGING-DEMO-01')->exists()) {
            return;
        }

        $supplier = Supplier::firstOrCreate(
            ['phone' => '0110000099'],
            [
                'name' => 'مورد أعمار الذمم — تجريبي',
                'email' => 'aging-supplier@demo.com',
                'address' => 'الرياض — بيانات تجريبية لتقرير أعمار الذمم',
                'opening_balance' => 0,
                'is_active' => true,
            ]
        );

        $branch = $branches->first();
        $warehouse = $warehouses->firstWhere('branch_id', $branch?->id) ?? $warehouses->first();
        $product = $products->first();

        if (! $branch || ! $warehouse || ! $product) {
            $this->command?->warn('PurchaseSeeder: تعذر إنشاء بيانات أعمار الذمم — بيانات أساسية ناقصة.');

            return;
        }

        $scenarios = [
            ['number' => 'PUR-AGING-DEMO-01', 'days' => 15, 'qty' => 10, 'notes' => 'ذمم 0–30 يوم'],
            ['number' => 'PUR-AGING-DEMO-02', 'days' => 45, 'qty' => 12, 'notes' => 'ذمم 31–60 يوم'],
            ['number' => 'PUR-AGING-DEMO-03', 'days' => 75, 'qty' => 15, 'notes' => 'ذمم 61–90 يوم'],
            ['number' => 'PUR-AGING-DEMO-04', 'days' => 120, 'qty' => 18, 'notes' => 'ذمم أكثر من 90 يوم'],
        ];

        foreach ($scenarios as $scenario) {
            $invoiceDate = Carbon::today()->subDays($scenario['days']);

            $invoice = PurchaseInvoice::create([
                'number' => $scenario['number'],
                'invoice_date' => $invoiceDate,
                'branch_id' => $branch->id,
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'tax_rate' => 15,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'status' => PurchaseInvoice::STATUS_DRAFT,
                'payment_status' => PurchaseInvoice::PAYMENT_STATUS_PENDING,
                'user_id' => $user->id,
                'notes' => $scenario['notes'],
            ]);

            $unitPrice = (float) ($product->cost_price ?: $product->base_price ?: 10);
            PurchaseInvoiceItem::create([
                'purchase_invoice_id' => $invoice->id,
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'quantity' => $scenario['qty'],
                'unit_price' => $unitPrice,
                'total' => round($scenario['qty'] * $unitPrice, 2),
            ]);

            $invoice->recalculateTotals();

            try {
                $invoice->confirm();
            } catch (\Throwable $e) {
                $invoice->delete();
                $this->command?->warn("PurchaseSeeder: تعذر إنشاء فاتورة أعمار الذمم {$scenario['number']}: {$e->getMessage()}");
            }
        }

        $this->command?->info('PurchaseSeeder: تم إنشاء فواتير تجريبية لتقرير أعمار ذمم الموردين.');
    }
}
