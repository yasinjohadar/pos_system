<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->string('check_number')->comment('رقم الشيك');
            $table->decimal('amount', 15, 2);
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('bank_name')->nullable()->comment('اسم البنك إن لم يكن مرتبطاً بحساب');
            $table->date('due_date')->comment('تاريخ الاستحقاق');
            $table->string('status', 30)->default('under_collection')->comment('under_collection, collected, returned');
            $table->foreignId('sale_payment_id')->nullable()->constrained('sale_payments')->nullOnDelete();
            $table->foreignId('supplier_payment_id')->nullable()->constrained('supplier_payments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
