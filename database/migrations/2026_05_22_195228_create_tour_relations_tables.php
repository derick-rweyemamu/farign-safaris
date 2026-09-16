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
        Schema::create('destination_tour', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('accommodation_tour', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('activity_tour', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('meal_plan_tour', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_plan_id')->constrained()->cascadeOnDelete();
        });
        Schema::create('tour_transport', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_id')->constrained('transports')->cascadeOnDelete();
        });
        Schema::create('animal_tour_itinerary', function (Blueprint $table) {
            $table->foreignId('tour_itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('animal_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animal_tour_itinerary');
        Schema::dropIfExists('tour_transport');
        Schema::dropIfExists('meal_plan_tour');
        Schema::dropIfExists('activity_tour');
        Schema::dropIfExists('accommodation_tour');
        Schema::dropIfExists('destination_tour');
    }
};
