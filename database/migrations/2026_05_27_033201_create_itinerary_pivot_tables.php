<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_tour_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('destination_itinerary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('accommodation_itinerary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('activity_itinerary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('activity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('itinerary_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transport_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('itinerary_meal_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meal_plan_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_meal_plan');
        Schema::dropIfExists('itinerary_transport');
        Schema::dropIfExists('activity_itinerary');
        Schema::dropIfExists('accommodation_itinerary');
        Schema::dropIfExists('destination_itinerary');
        Schema::dropIfExists('itinerary_tour_category');
    }
};
