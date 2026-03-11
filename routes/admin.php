<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockMovementController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\InventoryCountController;
use App\Http\Controllers\Admin\AppStorageController;
use App\Http\Controllers\Admin\AppStorageAnalyticsController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BackupScheduleController;
use App\Http\Controllers\Admin\BackupStorageController;
use App\Http\Controllers\Admin\BackupStorageAnalyticsController;
use App\Http\Controllers\Admin\StorageDiskMappingController;
use App\Http\Controllers\Admin\WhatsAppSettingsController;
use App\Http\Controllers\Admin\WhatsAppMessageController;
use App\Http\Controllers\Admin\WhatsAppWebController;
use App\Http\Controllers\Admin\WhatsAppWebSettingsController;
use App\Http\Controllers\Admin\WhatsAppWebhookController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SaleInvoiceController;
use App\Http\Controllers\Admin\SaleReturnController;
use App\Http\Controllers\Admin\TreasuryController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\FinancialTransferController;
use App\Http\Controllers\Admin\CheckController;
use App\Http\Controllers\Admin\CashVoucherController;
use App\Http\Controllers\Admin\FiscalYearController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseInvoiceController;
use App\Http\Controllers\Admin\PurchaseReturnController;
use App\Http\Controllers\Admin\Reports\SalesReportController;
use App\Http\Controllers\Admin\Reports\PurchaseReportController;
use App\Http\Controllers\Admin\Reports\ProfitReportController;
use App\Http\Controllers\Admin\Reports\InventoryReportController;
use App\Http\Controllers\Admin\Reports\PartnerReportController;
use App\Http\Controllers\Admin\Reports\TaxReportController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\PriceListController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'check.user.active'])->prefix('admin')->name('admin.')->group(function () {
    // الفروع والمخازن
    Route::resource('branches', BranchController::class);
    Route::resource('warehouses', WarehouseController::class);

    // التصنيفات والوحدات والمنتجات
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);
    Route::resource('products', ProductController::class);

    // إدارة المخزون
    Route::prefix('stock')->name('stock.')->group(function () {
        Route::get('movements', [StockMovementController::class, 'index'])->name('movements.index');
        Route::get('movements/create', [StockMovementController::class, 'create'])->name('movements.create');
        Route::post('movements', [StockMovementController::class, 'store'])->name('movements.store');
        Route::get('balances', [StockMovementController::class, 'balances'])->name('balances.index');
        Route::get('transfers', [StockTransferController::class, 'index'])->name('transfers.index');
        Route::get('transfers/create', [StockTransferController::class, 'create'])->name('transfers.create');
        Route::post('transfers', [StockTransferController::class, 'store'])->name('transfers.store');
        Route::get('transfers/{transfer}', [StockTransferController::class, 'show'])->name('transfers.show');
        Route::get('inventory-count', [InventoryCountController::class, 'index'])->name('inventory-count.index');
        Route::post('inventory-count', [InventoryCountController::class, 'store'])->name('inventory-count.store');
    });

    // التقارير
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales/daily', [SalesReportController::class, 'daily'])->name('sales.daily');
        Route::get('sales/monthly', [SalesReportController::class, 'monthly'])->name('sales.monthly');

        Route::get('purchases/daily', [PurchaseReportController::class, 'daily'])->name('purchases.daily');
        Route::get('purchases/monthly', [PurchaseReportController::class, 'monthly'])->name('purchases.monthly');

        Route::get('profit', [ProfitReportController::class, 'index'])->name('profit.index');
        Route::get('inventory', [InventoryReportController::class, 'index'])->name('inventory.index');
        Route::get('inventory/reorder', [InventoryReportController::class, 'reorder'])->name('inventory.reorder');
        Route::get('customers/aging', [PartnerReportController::class, 'customersAging'])->name('customers.aging');
        Route::get('suppliers/aging', [PartnerReportController::class, 'suppliersAging'])->name('suppliers.aging');
        Route::get('taxes', [TaxReportController::class, 'index'])->name('taxes.index');
    });

    // المبيعات
    Route::resource('payment-methods', PaymentMethodController::class)->except(['show']);
    Route::get('customers/{customer}/statement', [CustomerController::class, 'statement'])->name('customers.statement');
    Route::resource('customers', CustomerController::class);
    Route::get('sale-invoices/product-price', [SaleInvoiceController::class, 'getProductPrice'])->name('sale-invoices.product-price');
    Route::resource('sale-invoices', SaleInvoiceController::class)->names([
        'index' => 'sale-invoices.index',
        'create' => 'sale-invoices.create',
        'store' => 'sale-invoices.store',
        'show' => 'sale-invoices.show',
        'edit' => 'sale-invoices.edit',
        'update' => 'sale-invoices.update',
        'destroy' => 'sale-invoices.destroy',
    ]);
    Route::post('sale-invoices/{saleInvoice}/confirm', [SaleInvoiceController::class, 'confirm'])->name('sale-invoices.confirm');
    Route::post('sale-invoices/{saleInvoice}/payments', [SaleInvoiceController::class, 'addPayment'])->name('sale-invoices.payments.store');
    Route::delete('sale-invoices/{saleInvoice}/payments/{payment}', [SaleInvoiceController::class, 'destroyPayment'])->name('sale-invoices.payments.destroy');
    Route::resource('sale-returns', SaleReturnController::class)->only(['index', 'create', 'store', 'show'])->names([
        'index' => 'sale-returns.index',
        'create' => 'sale-returns.create',
        'store' => 'sale-returns.store',
        'show' => 'sale-returns.show',
    ]);
    Route::post('sale-returns/{saleReturn}/complete', [SaleReturnController::class, 'complete'])->name('sale-returns.complete');

    Route::resource('promotions', PromotionController::class)->except(['show']);
    Route::resource('price-lists', PriceListController::class)->except(['show']);

    Route::resource('treasuries', TreasuryController::class)->except(['show']);

    Route::resource('bank-accounts', BankAccountController::class)->except(['show']);

    Route::get('financial-transfers', [FinancialTransferController::class, 'index'])->name('financial-transfers.index');
    Route::get('financial-transfers/create', [FinancialTransferController::class, 'create'])->name('financial-transfers.create');
    Route::post('financial-transfers', [FinancialTransferController::class, 'store'])->name('financial-transfers.store');

    Route::get('checks', [CheckController::class, 'index'])->name('checks.index');
    Route::get('checks/create', [CheckController::class, 'create'])->name('checks.create');
    Route::post('checks', [CheckController::class, 'store'])->name('checks.store');
    Route::get('checks/{check}', [CheckController::class, 'show'])->name('checks.show');
    Route::post('checks/{check}/status', [CheckController::class, 'updateStatus'])->name('checks.update-status');

    Route::get('cash-vouchers', [CashVoucherController::class, 'index'])->name('cash-vouchers.index');
    Route::get('cash-vouchers/create', [CashVoucherController::class, 'create'])->name('cash-vouchers.create');
    Route::post('cash-vouchers', [CashVoucherController::class, 'store'])->name('cash-vouchers.store');

    Route::get('fiscal-years', [FiscalYearController::class, 'index'])->name('fiscal-years.index');
    Route::get('fiscal-years/create', [FiscalYearController::class, 'create'])->name('fiscal-years.create');
    Route::post('fiscal-years', [FiscalYearController::class, 'store'])->name('fiscal-years.store');
    Route::post('fiscal-years/{fiscalYear}/close', [FiscalYearController::class, 'close'])->name('fiscal-years.close');

    // المشتريات
    Route::get('suppliers/{supplier}/statement', [SupplierController::class, 'statement'])->name('suppliers.statement');
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchase-invoices', PurchaseInvoiceController::class)->names([
        'index' => 'purchase-invoices.index',
        'create' => 'purchase-invoices.create',
        'store' => 'purchase-invoices.store',
        'show' => 'purchase-invoices.show',
        'edit' => 'purchase-invoices.edit',
        'update' => 'purchase-invoices.update',
        'destroy' => 'purchase-invoices.destroy',
    ]);
    Route::post('purchase-invoices/{purchaseInvoice}/confirm', [PurchaseInvoiceController::class, 'confirm'])->name('purchase-invoices.confirm');
    Route::post('purchase-invoices/{purchaseInvoice}/payments', [PurchaseInvoiceController::class, 'addPayment'])->name('purchase-invoices.payments.store');
    Route::delete('purchase-invoices/{purchaseInvoice}/payments/{payment}', [PurchaseInvoiceController::class, 'destroyPayment'])->name('purchase-invoices.payments.destroy');
    Route::resource('purchase-returns', PurchaseReturnController::class)->only(['index', 'create', 'store', 'show'])->names([
        'index' => 'purchase-returns.index',
        'create' => 'purchase-returns.create',
        'store' => 'purchase-returns.store',
        'show' => 'purchase-returns.show',
    ]);
    Route::post('purchase-returns/{purchaseReturn}/complete', [PurchaseReturnController::class, 'complete'])->name('purchase-returns.complete');

    // ========== Email Settings Routes ==========
    Route::prefix('settings/email')->name('settings.email.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\EmailSettingController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\EmailSettingController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\EmailSettingController::class, 'store'])->name('store');
        Route::post('/test-temp', [\App\Http\Controllers\Admin\EmailSettingController::class, 'testTemp'])->name('test-temp');
        Route::get('/{emailSetting}/edit', [\App\Http\Controllers\Admin\EmailSettingController::class, 'edit'])->name('edit');
        Route::put('/{emailSetting}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'update'])->name('update');
        Route::delete('/{emailSetting}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'destroy'])->name('destroy');
        Route::post('/{emailSetting}/activate', [\App\Http\Controllers\Admin\EmailSettingController::class, 'activate'])->name('activate');
        Route::post('/{emailSetting}/test', [\App\Http\Controllers\Admin\EmailSettingController::class, 'test'])->name('test');
        Route::get('/provider/{provider}', [\App\Http\Controllers\Admin\EmailSettingController::class, 'getProviderPreset'])->name('provider.preset');
    });

    // ========== App Storage Routes ==========
    Route::prefix('storage')->name('storage.')->group(function () {
        Route::get('/', [AppStorageController::class, 'index'])->name('index');
        Route::get('/create', [AppStorageController::class, 'create'])->name('create');
        Route::post('/', [AppStorageController::class, 'store'])->name('store');
        Route::get('/{config}/edit', [AppStorageController::class, 'edit'])->name('edit');
        Route::put('/{config}', [AppStorageController::class, 'update'])->name('update');
        Route::delete('/{config}', [AppStorageController::class, 'destroy'])->name('destroy');
        Route::post('/{config}/test', [AppStorageController::class, 'test'])->name('test');
        Route::post('/test-connection', [AppStorageController::class, 'testConnection'])->name('test-connection');
        Route::get('/analytics', [AppStorageAnalyticsController::class, 'index'])->name('analytics');
    });

    // ========== Backup Routes ==========
    Route::prefix('backups')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::get('/create', [BackupController::class, 'create'])->name('create');
        Route::post('/', [BackupController::class, 'store'])->name('store');
        Route::get('/{backup}', [BackupController::class, 'show'])->name('show');
        Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
        Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
        Route::post('/{backup}/restore', [BackupController::class, 'restore'])->name('restore');
        Route::get('/{backup}/status', [BackupController::class, 'status'])->name('status');
        Route::post('/{backup}/run', [BackupController::class, 'run'])->name('run');
    });

    // ========== Backup Schedule Routes ==========
    Route::prefix('backup-schedules')->name('backup-schedules.')->group(function () {
        Route::get('/', [BackupScheduleController::class, 'index'])->name('index');
        Route::get('/create', [BackupScheduleController::class, 'create'])->name('create');
        Route::post('/', [BackupScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}/edit', [BackupScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [BackupScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [BackupScheduleController::class, 'destroy'])->name('destroy');
        Route::post('/{schedule}/execute', [BackupScheduleController::class, 'execute'])->name('execute');
        Route::post('/{schedule}/toggle-active', [BackupScheduleController::class, 'toggleActive'])->name('toggle-active');
    });

    // ========== Backup Storage Routes ==========
    Route::prefix('backup-storage')->name('backup-storage.')->group(function () {
        Route::get('/', [BackupStorageController::class, 'index'])->name('index');
        Route::get('/create', [BackupStorageController::class, 'create'])->name('create');
        Route::post('/', [BackupStorageController::class, 'store'])->name('store');
        Route::get('/{config}/edit', [BackupStorageController::class, 'edit'])->name('edit');
        Route::put('/{config}', [BackupStorageController::class, 'update'])->name('update');
        Route::delete('/{config}', [BackupStorageController::class, 'destroy'])->name('destroy');
        Route::post('/{config}/test', [BackupStorageController::class, 'test'])->name('test');
        Route::post('/test-connection', [BackupStorageController::class, 'testConnection'])->name('test-connection');
        Route::get('/analytics', [BackupStorageAnalyticsController::class, 'index'])->name('analytics');
    });

    // ========== Storage Disk Mappings Routes ==========
    Route::prefix('storage-disk-mappings')->name('storage-disk-mappings.')->group(function () {
        Route::get('/', [StorageDiskMappingController::class, 'index'])->name('index');
        Route::get('/create', [StorageDiskMappingController::class, 'create'])->name('create');
        Route::post('/', [StorageDiskMappingController::class, 'store'])->name('store');
        Route::get('/{mapping}/edit', [StorageDiskMappingController::class, 'edit'])->name('edit');
        Route::put('/{mapping}', [StorageDiskMappingController::class, 'update'])->name('update');
        Route::delete('/{mapping}', [StorageDiskMappingController::class, 'destroy'])->name('destroy');
    });



    Route::prefix('ai')->name('ai.')->group(function () {
    
        // AI Models
        Route::resource('models', \App\Http\Controllers\Admin\AIModelController::class)->names([
            'index' => 'models.index',
            'create' => 'models.create',
            'store' => 'models.store',
            'show' => 'models.show',
            'edit' => 'models.edit',
            'update' => 'models.update',
            'destroy' => 'models.destroy',
        ]);
        Route::post('models/{model}/test', [\App\Http\Controllers\Admin\AIModelController::class, 'test'])->name('models.test');
        Route::post('models/test-temp', [\App\Http\Controllers\Admin\AIModelController::class, 'testTemp'])->name('models.test-temp');
        Route::post('models/{model}/set-default', [\App\Http\Controllers\Admin\AIModelController::class, 'setDefault'])->name('models.set-default');
        Route::post('models/{model}/toggle-active', [\App\Http\Controllers\Admin\AIModelController::class, 'toggleActive'])->name('models.toggle-active');
        Route::post('models/fetch-groq-models', [\App\Http\Controllers\Admin\AIModelController::class, 'fetchGroqModels'])->name('models.fetch-groq-models');
        
        // Content
        Route::post('content/summarize', [\App\Http\Controllers\Admin\AIContentController::class, 'summarize'])->name('content.summarize');
        Route::post('content/improve', [\App\Http\Controllers\Admin\AIContentController::class, 'improve'])->name('content.improve');
        Route::post('content/grammar-check', [\App\Http\Controllers\Admin\AIContentController::class, 'grammarCheck'])->name('content.grammar-check');
        
        // Settings
        Route::get('settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'index'])->name('settings.index');
        Route::put('settings', [\App\Http\Controllers\Admin\AISettingsController::class, 'update'])->name('settings.update');
    });
    
    // WhatsApp Settings Routes
    Route::prefix('whatsapp-settings')
        ->middleware(['role:admin'])
        ->name('whatsapp-settings.')
        ->group(function () {
            Route::get('/', [WhatsAppSettingsController::class, 'index'])->name('index');
            Route::post('/', [WhatsAppSettingsController::class, 'update'])->name('update');
            Route::post('/test-connection', [WhatsAppSettingsController::class, 'testConnection'])->name('test-connection');
        });

    // WhatsApp Messages Routes
    Route::prefix('whatsapp-messages')
        ->middleware(['role:admin'])
        ->name('whatsapp-messages.')
        ->group(function () {
            Route::get('/', [WhatsAppMessageController::class, 'index'])->name('index');
            Route::get('/send', [WhatsAppMessageController::class, 'create'])->name('create');
            Route::get('/search-students', [WhatsAppMessageController::class, 'searchStudents'])->name('search-students');
            Route::post('/send', [WhatsAppMessageController::class, 'send'])->name('send');
            Route::post('/broadcast', [WhatsAppMessageController::class, 'broadcast'])->name('broadcast');
            Route::get('/broadcast/students-count', [WhatsAppMessageController::class, 'getStudentsCount'])->name('broadcast.students-count');
            Route::post('/{message}/retry', [WhatsAppMessageController::class, 'retry'])->name('retry');
            Route::get('/{message}', [WhatsAppMessageController::class, 'show'])->name('show');
        });

    // WhatsApp Web Routes
    Route::prefix('whatsapp-web')
        ->middleware(['role:admin'])
        ->name('whatsapp-web.')
        ->group(function () {
            Route::get('/connect', [WhatsAppWebController::class, 'connect'])->name('connect');
            Route::post('/start-connection', [WhatsAppWebController::class, 'startConnection'])->name('start-connection');
            Route::get('/qr/{sessionId}', [WhatsAppWebController::class, 'getQrCode'])->name('qr');
            Route::get('/status/{sessionId}', [WhatsAppWebController::class, 'getStatus'])->name('status');
            Route::post('/disconnect/{sessionId}', [WhatsAppWebController::class, 'disconnect'])->name('disconnect');
        });

    // WhatsApp Web Settings Routes
    Route::prefix('whatsapp-web-settings')
        ->middleware(['role:admin'])
        ->name('whatsapp-web-settings.')
        ->group(function () {
            Route::get('/', [WhatsAppWebSettingsController::class, 'index'])->name('index');
            Route::post('/', [WhatsAppWebSettingsController::class, 'update'])->name('update');
            Route::post('/test-connection', [WhatsAppWebSettingsController::class, 'testConnection'])->name('test-connection');
        });

});
