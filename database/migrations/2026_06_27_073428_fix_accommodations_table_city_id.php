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
        Schema::table('accommodations', function (Blueprint $table) {
            if (Schema::hasColumn('accommodations', 'destination_id')) {
                $table->dropForeign(['destination_id']);
                $table->dropColumn('destination_id');
            }
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn('city_id');
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->cascadeOnDelete();
        });
    }
};
