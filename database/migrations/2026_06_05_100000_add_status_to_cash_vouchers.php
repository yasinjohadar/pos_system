<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_vouchers', function (Blueprint $table) {
            $table->string('status', 20)->default('active')->after('notes');
            $table->timestamp('cancelled_at')->nullable()->after('status');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_vouchers', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn(['status', 'cancelled_at', 'cancelled_by']);
        });
    }
};
