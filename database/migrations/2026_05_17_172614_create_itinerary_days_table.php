<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: itinerary_days + prices + missing fields
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itinerary_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->constrained()->onDelete('cascade');
            $table->string('day'); // "Day 1" or "Day 4 to 6"
            $table->date('date')->nullable(); // Optional specific date
            $table->foreignId('destination_id')->nullable()->constrained()->onDelete('set null');
            $table->string('location')->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('accommodation_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('accommodation_amount', 10, 2)->nullable();
            $table->string('accommodation')->nullable();
            $table->foreignId('meal_plan_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('meal_plan_amount', 10, 2)->nullable();
            $table->decimal('adult_price', 15, 2)->nullable();
            $table->decimal('child_price', 15, 2)->nullable();
            $table->string('meals')->nullable();
            $table->string('featured_image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itinerary_days');
    }
};
