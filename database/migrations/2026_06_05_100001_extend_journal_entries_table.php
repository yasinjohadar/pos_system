<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->string('source', 20)->default('auto')->after('is_posted');
            $table->foreignId('reversed_entry_id')->nullable()->after('source')->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->after('reversed_entry_id')->constrained('fiscal_years')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropForeign(['reversed_entry_id']);
            $table->dropForeign(['fiscal_year_id']);
            $table->dropColumn(['source', 'reversed_entry_id', 'fiscal_year_id']);
        });
    }
};
