<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 15, 4);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('min_invoice_amount', 15, 4)->nullable();
            $table->decimal('min_qty', 15, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promotion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('max_qty', 15, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_items');
        Schema::dropIfExists('promotions');
    }
};

