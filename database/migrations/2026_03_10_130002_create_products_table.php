<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('barcode')->nullable()->comment('فريد أو غير فريد حسب نوع المنتج');
            $table->text('description')->nullable();
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('min_stock_alert')->default(0)->comment('حد تنبيه انخفاض المخزون');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('barcode');
            $table->index(['category_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
