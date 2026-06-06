<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_quotes', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 8, 4)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('status', 20)->default('draft');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('converted_invoice_id')->nullable()->constrained('sale_invoices')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_quote_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_quote_items');
        Schema::dropIfExists('sales_quotes');
    }
};
