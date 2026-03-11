<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_invoice_id')->constrained('sale_invoices')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->foreignId('payment_method_id')->constrained('payment_methods')->cascadeOnDelete();
            $table->date('payment_date');
            $table->string('reference')->nullable()->comment('رقم شيك أو تحويل');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['sale_invoice_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
