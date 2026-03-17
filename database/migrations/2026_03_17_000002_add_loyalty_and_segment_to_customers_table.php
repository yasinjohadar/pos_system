<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('segment_id')
                ->nullable()
                ->after('price_list_id')
                ->constrained('customer_segments')
                ->nullOnDelete();
            $table->integer('loyalty_points')->default(0)->after('segment_id');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('segment_id');
            $table->dropColumn('loyalty_points');
        });
    }
};
