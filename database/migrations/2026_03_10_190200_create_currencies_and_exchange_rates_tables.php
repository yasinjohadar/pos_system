<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->string('symbol', 10)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();
            $table->decimal('rate', 18, 6)->default(1);
            $table->date('valid_from');
            $table->timestamps();
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('branch_id')->constrained('currencies')->nullOnDelete();
            $table->decimal('exchange_rate', 18, 6)->default(1)->after('currency_id');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('branch_id')->constrained('currencies')->nullOnDelete();
            $table->decimal('exchange_rate', 18, 6)->default(1)->after('currency_id');
        });

        Schema::table('cash_vouchers', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('fiscal_year_id')->constrained('currencies')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_vouchers', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'exchange_rate']);
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropColumn(['currency_id', 'exchange_rate']);
        });

        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('currencies');
    }
};

