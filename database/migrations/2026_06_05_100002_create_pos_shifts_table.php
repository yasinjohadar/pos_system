<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('treasury_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->nullable();
            $table->decimal('expected_cash', 15, 2)->nullable();
            $table->decimal('cash_difference', 15, 2)->nullable();
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->string('status', 20)->default('open');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_held_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pos_shift_id')->nullable()->constrained('pos_shifts')->nullOnDelete();
            $table->string('reference', 50);
            $table->json('cart_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_held_sales');
        Schema::dropIfExists('pos_shifts');
    }
};
