<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number');
            $table->date('expiry_date')->nullable();
            $table->decimal('initial_quantity', 15, 4);
            $table->decimal('current_quantity', 15, 4);
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->date('received_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'warehouse_id', 'batch_number']);
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
