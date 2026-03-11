<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * نوع الحركة: in, out, transfer_out, transfer_in, adjustment, inventory_count, return_sale, return_purchase
     */
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('type', 30)->comment('in, out, transfer_out, transfer_in, adjustment, inventory_count, return_sale, return_purchase');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->decimal('quantity', 15, 4)->comment('موجب للإدخال، سالب للصرف');
            $table->string('reference_type', 50)->nullable()->comment('sale_invoice, purchase_invoice, stock_transfer, inventory_count');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('stock_transfer_id')->nullable()->constrained('stock_transfers')->nullOnDelete();
            $table->date('movement_date');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'warehouse_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
