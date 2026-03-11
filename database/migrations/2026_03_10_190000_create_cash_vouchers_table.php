<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('receipt, payment');
            $table->string('voucher_number')->unique();
            $table->date('date');
            $table->foreignId('treasury_id')->nullable()->constrained('treasuries')->nullOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->nullable();
            $table->string('category')->nullable()->comment('مصروف، إيراد آخر، ...');
            $table->string('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_vouchers');
    }
};

