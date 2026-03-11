<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique()->comment('رقم الفاتورة');
            $table->date('invoice_date');
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete()->comment('مخزن الصرف الافتراضي');
            $table->decimal('subtotal', 15, 2)->default(0)->comment('المجموع قبل الضريبة والخصم');
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('نسبة الضريبة %');
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->string('discount_type', 20)->default('fixed')->comment('percent, fixed');
            $table->decimal('discount_value', 15, 2)->default(0)->comment('نسبة أو مبلغ الخصم');
            $table->decimal('discount_amount', 15, 2)->default(0)->comment('قيمة الخصم المحسوبة');
            $table->decimal('total', 15, 2)->default(0)->comment('الصافي');
            $table->string('payment_status', 20)->default('pending')->comment('pending, partial, paid');
            $table->string('status', 20)->default('draft')->comment('draft, confirmed, cancelled');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'invoice_date']);
            $table->index(['status', 'payment_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_invoices');
    }
};
