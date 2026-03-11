<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_id')->constrained('price_lists')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('price', 15, 4);
            $table->timestamps();

            $table->unique(['price_list_id', 'product_id']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('price_list_id')
                ->nullable()
                ->after('opening_balance')
                ->constrained('price_lists')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('price_list_id');
        });

        Schema::dropIfExists('price_list_items');
        Schema::dropIfExists('price_lists');
    }
};

