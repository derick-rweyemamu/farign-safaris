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
        Schema::table('itinerary_day_activity', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('time_of_day');
        });

        Schema::table('itinerary_day_transport', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('time_of_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itinerary_day_transport', function (Blueprint $table) {
            $table->dropColumn('amount');
        });

        Schema::table('itinerary_day_activity', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
