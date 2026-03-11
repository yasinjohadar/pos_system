<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_treasury_id')->nullable()->constrained('treasuries')->nullOnDelete();
            $table->foreignId('from_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('to_treasury_id')->nullable()->constrained('treasuries')->nullOnDelete();
            $table->foreignId('to_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('transfer_date');
            $table->string('reference')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transfers');
    }
};
