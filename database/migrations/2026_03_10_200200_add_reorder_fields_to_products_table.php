<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('reorder_level', 15, 4)->nullable()->after('min_stock_alert');
            $table->decimal('max_level', 15, 4)->nullable()->after('reorder_level');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['reorder_level', 'max_level']);
        });
    }
};

