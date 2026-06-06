<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\CustomerSegment;
use App\Models\PaymentMethod;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionItem;
use App\Models\SaleInvoice;
use App\Models\SaleInvoiceItem;
use App\Models\SalePayment;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Treasury;
use App\Models\User;
use App\Models\Warehouse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->first();
        $branches = Branch::where('is_active', true)->orderBy('id')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('id')->get();
        $products = Product::where('is_active', true)->where('base_price', '>', 0)->orderBy('id')->limit(30)->get();
        $cashMethod = PaymentMethod::where('code', PaymentMethod::CODE_CASH)->first();
        $treasury = Treasury::where('is_active', true)->first();

        if (! $user || $branches->isEmpty() || $warehouses->isEmpty() || $products->isEmpty()) {
            $this->command?->warn('SalesSeeder: يتطلب مستخدماً وفروعاً ومخازن ومنتجات.');

            return;
        }

        $segments = $this->seedSegments();
        $customers = $this->seedCustomers($segments);
        $this->seedCoupons();
        $this->seedPriceLists($products);
        $this->seedPromotions($products);
        $this->seedBankAccounts($branches);

        if (SaleInvoice::exists()) {
            $this->command?->info('SalesSeeder: فواتير موجودة — تخطي إنشاء الفواتير.');
            $this->seedReturns($user);
            $this->seedReportSnapshots($user, $branches, $warehouses, $products, $customers);
            $this->seedAgingSnapshots($user, $branches, $warehouses, $products);

            return;
        }

        DB::transaction(function () use ($user, $branches, $warehouses, $products, $cashMethod, $treasury, $customers) {
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
                $customer = $customers[$index % $customers->count()];
                $invoiceDate = Carbon::today()->subDays($scenario['days']);

                $invoice = SaleInvoice::create([
                    'number' => 'INV-DEMO-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'invoice_date' => $invoiceDate,
                    'branch_id' => $branch->id,
                    'customer_id' => $customer->id,
                    'warehouse_id' => $warehouse->id,
                    'tax_rate' => $scenario['tax'],
                    'discount_type' => 'fixed',
                    'discount_value' => 0,
                    'status' => SaleInvoice::STATUS_DRAFT,
                    'payment_status' => SaleInvoice::PAYMENT_STATUS_PENDING,
                    'user_id' => $user->id,
                    'notes' => 'فاتورة تجريبية #' . ($index + 1),
                ]);

                $itemCount = random_int(1, 3);
                $picked = $products->random(min($itemCount, $products->count()));

                foreach ($picked as $product) {
                    $qty = random_int(1, 3);
                    $unitPrice = (float) $product->base_price;
                    SaleInvoiceItem::create([
                        'sale_invoice_id' => $invoice->id,
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
                        $invoice->update(['status' => SaleInvoice::STATUS_DRAFT]);
                        $this->command?->warn("SalesSeeder: تعذر تأكيد فاتورة {$invoice->number}: {$e->getMessage()}");
                        continue;
                    }

                    if ($cashMethod && $treasury && in_array($scenario['payment'], ['paid', 'partial'], true)) {
                        $amount = $scenario['payment'] === 'paid'
                            ? (float) $invoice->total
                            : round((float) $invoice->total * 0.5, 2);

                        SalePayment::create([
                            'sale_invoice_id' => $invoice->id,
                            'amount' => $amount,
                            'payment_method_id' => $cashMethod->id,
                            'treasury_id' => $treasury->id,
                            'payment_date' => $invoiceDate,
                            'user_id' => $user->id,
                            'notes' => 'دفعة تجريبية',
                        ]);
                    }
                }
            }

            $this->seedReturns($user);
            $this->seedReportSnapshots($user, $branches, $warehouses, $products, $customers);
            $this->seedAgingSnapshots($user, $branches, $warehouses, $products);
        });

        $this->command?->info('SalesSeeder: تم إنشاء بيانات المبيعات التجريبية.');
    }

    private function seedSegments(): \Illuminate\Support\Collection
    {
        $data = [
            ['name' => 'عملاء VIP', 'name_en' => 'VIP', 'color' => '#7c3aed', 'description' => 'عملاء مميزون بمشتريات عالية'],
            ['name' => 'تجزئة', 'name_en' => 'Retail', 'color' => '#2563eb', 'description' => 'عملاء التجزئة العاديون'],
            ['name' => 'جملة', 'name_en' => 'Wholesale', 'color' => '#059669', 'description' => 'عملاء الجملة والموردون'],
        ];

        return collect($data)->map(fn ($row) => CustomerSegment::firstOrCreate(
            ['name' => $row['name']],
            array_merge($row, ['is_active' => true])
        ));
    }

    private function seedCustomers(\Illuminate\Support\Collection $segments): \Illuminate\Support\Collection
    {
        $names = [
            ['name' => 'أحمد محمد العلي', 'phone' => '0501234567', 'email' => 'ahmed@demo.com'],
            ['name' => 'فاطمة سعيد الحربي', 'phone' => '0559876543', 'email' => 'fatima@demo.com'],
            ['name' => 'خالد عبدالله القحطاني', 'phone' => '0541112233', 'email' => 'khaled@demo.com'],
            ['name' => 'نورة إبراهيم الشمري', 'phone' => '0564445566', 'email' => 'noura@demo.com'],
            ['name' => 'عمر يوسف الدوسري', 'phone' => '0537778899', 'email' => 'omar@demo.com'],
            ['name' => 'سارة حسن الزهراني', 'phone' => '0582223344', 'email' => 'sara@demo.com'],
            ['name' => 'محمد راشد الغامدي', 'phone' => '0596667788', 'email' => 'mohammed@demo.com'],
            ['name' => 'ريم فهد العتيبي', 'phone' => '0508889900', 'email' => 'reem@demo.com'],
            ['name' => 'يوسف ناصر المطيري', 'phone' => '0553334455', 'email' => 'youssef@demo.com'],
            ['name' => 'هند علي السبيعي', 'phone' => '0545556677', 'email' => 'hind@demo.com'],
            ['name' => 'عبدالرحمن سالم البقمي', 'phone' => '0561110099', 'email' => 'abdulrahman@demo.com'],
            ['name' => 'مريم خالد الحارثي', 'phone' => '0587778890', 'email' => 'mariam@demo.com'],
        ];

        return collect($names)->map(function ($row, $index) use ($segments) {
            return Customer::firstOrCreate(
                ['phone' => $row['phone']],
                [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'address' => 'الرياض — حي النخيل',
                    'segment_id' => $segments[$index % $segments->count()]->id,
                    'loyalty_points' => random_int(0, 500),
                    'opening_balance' => 0,
                    'is_active' => true,
                ]
            );
        });
    }

    private function seedCoupons(): void
    {
        $coupons = [
            ['code' => 'WELCOME10', 'type' => 'percent', 'value' => 10, 'min_purchase' => 100, 'max_uses' => 100],
            ['code' => 'SAVE50', 'type' => 'fixed', 'value' => 50, 'min_purchase' => 300, 'max_uses' => 50],
            ['code' => 'VIP20', 'type' => 'percent', 'value' => 20, 'min_purchase' => 500, 'max_uses' => 20],
            ['code' => 'SUMMER15', 'type' => 'percent', 'value' => 15, 'min_purchase' => 200, 'max_uses' => null],
        ];

        foreach ($coupons as $c) {
            Coupon::firstOrCreate(
                ['code' => $c['code']],
                array_merge($c, [
                    'used_count' => 0,
                    'valid_from' => Carbon::today()->subMonth(),
                    'valid_until' => Carbon::today()->addMonths(3),
                    'is_active' => true,
                    'description' => 'كوبون تجريبي — ' . $c['code'],
                ])
            );
        }
    }

    private function seedPriceLists($products): void
    {
        if (PriceList::exists()) {
            return;
        }

        $lists = [
            ['name' => 'أسعار الجملة', 'description' => 'خصم 10% عن سعر التجزئة للعملاء بالجملة'],
            ['name' => 'أسعار VIP', 'description' => 'أسعار خاصة لعملاء VIP'],
        ];

        foreach ($lists as $i => $listData) {
            $list = PriceList::create(array_merge($listData, ['is_active' => true]));
            $picked = $products->random(min(8, $products->count()));
            foreach ($picked as $product) {
                $discount = $i === 0 ? 0.9 : 0.85;
                PriceListItem::create([
                    'price_list_id' => $list->id,
                    'product_id' => $product->id,
                    'price' => round((float) $product->base_price * $discount, 2),
                ]);
            }
        }
    }

    private function seedPromotions($products): void
    {
        if (Promotion::exists()) {
            return;
        }

        $promos = [
            ['name' => 'عرض نهاية الأسبوع', 'type' => 'percent', 'value' => 15, 'min_qty' => 2],
            ['name' => 'خصم ثابت — منتجات مختارة', 'type' => 'fixed', 'value' => 25, 'min_qty' => 1],
            ['name' => 'عرض الصيف', 'type' => 'percent', 'value' => 10, 'min_qty' => null],
        ];

        foreach ($promos as $promoData) {
            $promo = Promotion::create(array_merge($promoData, [
                'start_date' => Carbon::today()->subWeek(),
                'end_date' => Carbon::today()->addMonths(2),
                'is_active' => true,
            ]));
            foreach ($products->random(min(5, $products->count())) as $product) {
                PromotionItem::create([
                    'promotion_id' => $promo->id,
                    'product_id' => $product->id,
                ]);
            }
        }
    }

    private function seedBankAccounts($branches): void
    {
        if (BankAccount::exists()) {
            return;
        }

        $branch = $branches->first();
        BankAccount::create([
            'name' => 'البنك الأهلي — حساب رئيسي',
            'account_number' => 'SA0380000000608010167519',
            'branch_id' => $branch?->id,
            'opening_balance' => 50000,
            'currency' => 'SAR',
            'is_active' => true,
        ]);
        BankAccount::create([
            'name' => 'بنك الراجحي — حساب تشغيلي',
            'account_number' => 'SA5880000000001234567890',
            'branch_id' => $branch?->id,
            'opening_balance' => 25000,
            'currency' => 'SAR',
            'is_active' => true,
        ]);
    }

    private function seedReturns(User $user): void
    {
        if (SaleReturn::exists()) {
            return;
        }

        $invoices = SaleInvoice::where('status', SaleInvoice::STATUS_CONFIRMED)
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

            $saleReturn = SaleReturn::create([
                'return_number' => 'RET-DEMO-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                'sale_invoice_id' => $invoice->id,
                'return_date' => $invoice->invoice_date->copy()->addDay(),
                'warehouse_id' => $invoice->warehouse_id,
                'subtotal_refund' => $refund,
                'tax_refund' => 0,
                'total_refund' => $refund,
                'status' => $index === 0 ? SaleReturn::STATUS_PENDING : SaleReturn::STATUS_COMPLETED,
                'user_id' => $user->id,
                'notes' => 'مرتجع تجريبي',
            ]);

            SaleReturnItem::create([
                'sale_return_id' => $saleReturn->id,
                'sale_invoice_item_id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $returnQty,
                'unit_price' => $item->unit_price,
                'total' => $refund,
            ]);

            if ($saleReturn->status === SaleReturn::STATUS_COMPLETED) {
                try {
                    $saleReturn->complete();
                } catch (\Throwable $e) {
                    $saleReturn->update(['status' => SaleReturn::STATUS_PENDING]);
                    $this->command?->warn("SalesSeeder: تعذر إكمال مرتجع {$saleReturn->return_number}: {$e->getMessage()}");
                }
            }
        }
    }

    /**
     * فواتير بتواريخ اليوم/أمس لمعاينة تقارير المبيعات.
     */
    private function seedReportSnapshots(
        User $user,
        \Illuminate\Support\Collection $branches,
        \Illuminate\Support\Collection $warehouses,
        \Illuminate\Support\Collection $products,
        \Illuminate\Support\Collection $customers
    ): void {
        $today = Carbon::today();
        $scenarios = [
            ['number' => 'INV-REPORT-TODAY-01', 'date' => $today, 'tax' => 15, 'item_count' => 2],
            ['number' => 'INV-REPORT-TODAY-02', 'date' => $today, 'tax' => 0, 'item_count' => 1],
            ['number' => 'INV-REPORT-YDAY-01', 'date' => $today->copy()->subDay(), 'tax' => 15, 'item_count' => 2],
        ];

        foreach ($scenarios as $index => $scenario) {
            if (SaleInvoice::where('number', $scenario['number'])->exists()) {
                continue;
            }

            $branch = $branches[$index % $branches->count()];
            $warehouse = $warehouses->firstWhere('branch_id', $branch->id) ?? $warehouses->first();
            $customer = $customers[$index % $customers->count()];

            $invoice = SaleInvoice::create([
                'number' => $scenario['number'],
                'invoice_date' => $scenario['date'],
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'warehouse_id' => $warehouse->id,
                'tax_rate' => $scenario['tax'],
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'status' => SaleInvoice::STATUS_DRAFT,
                'payment_status' => SaleInvoice::PAYMENT_STATUS_PENDING,
                'user_id' => $user->id,
                'notes' => 'فاتورة تجريبية للتقرير اليومي',
            ]);

            $picked = $products->random(min($scenario['item_count'], $products->count()));
            foreach ($picked as $product) {
                $qty = random_int(2, 5);
                $unitPrice = (float) $product->base_price;
                SaleInvoiceItem::create([
                    'sale_invoice_id' => $invoice->id,
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
                $this->command?->warn("SalesSeeder: تعذر إنشاء فاتورة تقرير {$scenario['number']}: {$e->getMessage()}");
            }
        }

        if (SaleReturn::where('return_number', 'RET-REPORT-TODAY-01')->exists()) {
            return;
        }

        $sourceInvoice = SaleInvoice::where('number', 'INV-REPORT-TODAY-02')
            ->where('status', SaleInvoice::STATUS_CONFIRMED)
            ->with('items')
            ->first();

        if (! $sourceInvoice || $sourceInvoice->items->isEmpty()) {
            return;
        }

        $item = $sourceInvoice->items->first();
        $returnQty = min(1, (float) $item->quantity);
        $refund = round($returnQty * (float) $item->unit_price, 2);

        $saleReturn = SaleReturn::create([
            'return_number' => 'RET-REPORT-TODAY-01',
            'sale_invoice_id' => $sourceInvoice->id,
            'return_date' => $today,
            'warehouse_id' => $sourceInvoice->warehouse_id,
            'subtotal_refund' => $refund,
            'tax_refund' => 0,
            'total_refund' => $refund,
            'status' => SaleReturn::STATUS_PENDING,
            'user_id' => $user->id,
            'notes' => 'مرتجع تجريبي للتقرير اليومي',
        ]);

        SaleReturnItem::create([
            'sale_return_id' => $saleReturn->id,
            'sale_invoice_item_id' => $item->id,
            'product_id' => $item->product_id,
            'quantity' => $returnQty,
            'unit_price' => $item->unit_price,
            'total' => $refund,
        ]);

        try {
            $saleReturn->complete();
        } catch (\Throwable $e) {
            $saleReturn->update(['status' => SaleReturn::STATUS_PENDING]);
            $this->command?->warn("SalesSeeder: تعذر إكمال مرتجع التقرير: {$e->getMessage()}");
        }
    }

    /**
     * فواتير آجلة بتواريخ متفرقة لمعاينة تقرير أعمار ديون العملاء.
     */
    private function seedAgingSnapshots(
        User $user,
        \Illuminate\Support\Collection $branches,
        \Illuminate\Support\Collection $warehouses,
        \Illuminate\Support\Collection $products
    ): void {
        if (SaleInvoice::where('number', 'INV-AGING-DEMO-01')->exists()) {
            return;
        }

        $segment = CustomerSegment::query()->first();
        $customer = Customer::firstOrCreate(
            ['phone' => '0500000099'],
            [
                'name' => 'شركة أعمار الديون — تجريبي',
                'email' => 'aging-demo@demo.com',
                'address' => 'الرياض — بيانات تجريبية لتقرير أعمار الديون',
                'segment_id' => $segment?->id,
                'loyalty_points' => 0,
                'opening_balance' => 0,
                'is_active' => true,
            ]
        );

        $branch = $branches->first();
        $warehouse = $warehouses->firstWhere('branch_id', $branch?->id) ?? $warehouses->first();
        $product = $products->first();

        if (! $branch || ! $warehouse || ! $product) {
            $this->command?->warn('SalesSeeder: تعذر إنشاء بيانات أعمار الديون — بيانات أساسية ناقصة.');

            return;
        }

        $scenarios = [
            ['number' => 'INV-AGING-DEMO-01', 'days' => 15, 'qty' => 3, 'notes' => 'دين 0–30 يوم'],
            ['number' => 'INV-AGING-DEMO-02', 'days' => 45, 'qty' => 4, 'notes' => 'دين 31–60 يوم'],
            ['number' => 'INV-AGING-DEMO-03', 'days' => 75, 'qty' => 5, 'notes' => 'دين 61–90 يوم'],
            ['number' => 'INV-AGING-DEMO-04', 'days' => 120, 'qty' => 6, 'notes' => 'دين أكثر من 90 يوم'],
        ];

        foreach ($scenarios as $scenario) {
            $invoiceDate = Carbon::today()->subDays($scenario['days']);

            $invoice = SaleInvoice::create([
                'number' => $scenario['number'],
                'invoice_date' => $invoiceDate,
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'warehouse_id' => $warehouse->id,
                'tax_rate' => 15,
                'discount_type' => 'fixed',
                'discount_value' => 0,
                'status' => SaleInvoice::STATUS_DRAFT,
                'payment_status' => SaleInvoice::PAYMENT_STATUS_PENDING,
                'user_id' => $user->id,
                'notes' => $scenario['notes'],
            ]);

            $unitPrice = (float) $product->base_price;
            SaleInvoiceItem::create([
                'sale_invoice_id' => $invoice->id,
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
                $this->command?->warn("SalesSeeder: تعذر إنشاء فاتورة أعمار الديون {$scenario['number']}: {$e->getMessage()}");
            }
        }

        $this->command?->info('SalesSeeder: تم إنشاء فواتير تجريبية لتقرير أعمار ديون العملاء.');
    }
}
