<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treasuries', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('type')->constrained('branches')->nullOnDelete();
            $table->decimal('opening_balance', 15, 2)->default(0)->after('branch_id');
            $table->string('currency', 10)->nullable()->after('opening_balance');
        });
    }

    public function down(): void
    {
        Schema::table('treasuries', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn(['opening_balance', 'currency']);
        });
    }
};
