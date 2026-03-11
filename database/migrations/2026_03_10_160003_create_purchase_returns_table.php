<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique()->comment('رقم أمر المرتجع');
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices')->cascadeOnDelete();
            $table->date('return_date');
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete()->comment('مخزن الصرف للمرتجع');
            $table->decimal('subtotal_refund', 15, 2)->default(0);
            $table->decimal('tax_refund', 15, 2)->default(0);
            $table->decimal('total_refund', 15, 2)->default(0);
            $table->string('status', 20)->default('pending')->comment('pending, completed, cancelled');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['purchase_invoice_id', 'return_date']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};
