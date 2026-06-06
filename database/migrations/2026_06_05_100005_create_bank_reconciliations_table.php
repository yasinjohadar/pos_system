<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained()->cascadeOnDelete();
            $table->date('statement_date');
            $table->decimal('statement_balance', 15, 2);
            $table->decimal('book_balance', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0);
            $table->string('status', 20)->default('draft');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('reconciled_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bank_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('journal_entry_line_id')->nullable()->constrained()->nullOnDelete();
            $table->date('transaction_date');
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->boolean('is_cleared')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_items');
        Schema::dropIfExists('bank_reconciliations');
    }
};
