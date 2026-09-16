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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('tour_id')->nullable()->constrained('tours')->nullOnDelete()->after('product_id');
            $table->integer('adults')->nullable()->default(1)->after('tax_applied');
            $table->integer('children')->nullable()->default(0)->after('adults');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn(['tour_id', 'adults', 'children']);
        });
    }
};
