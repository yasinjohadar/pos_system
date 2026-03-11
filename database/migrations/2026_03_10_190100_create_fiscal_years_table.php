<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable()->after('invoice_date')->constrained('fiscal_years')->nullOnDelete();
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable()->after('invoice_date')->constrained('fiscal_years')->nullOnDelete();
        });

        Schema::table('cash_vouchers', function (Blueprint $table) {
            $table->foreignId('fiscal_year_id')->nullable()->after('date')->constrained('fiscal_years')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cash_vouchers', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });

        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });

        Schema::table('sale_invoices', function (Blueprint $table) {
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn('fiscal_year_id');
        });

        Schema::dropIfExists('fiscal_years');
    }
};

