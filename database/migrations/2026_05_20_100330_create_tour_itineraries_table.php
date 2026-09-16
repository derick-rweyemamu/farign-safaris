<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: tour_itineraries + granular_costs
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->integer('day_number');
            $table->string('title');
            $table->text('description');
            $table->text('activities')->nullable();
            $table->text('accommodation')->nullable();
            $table->foreignId('accommodation_id')->nullable()->constrained('accommodations')->nullOnDelete();
            $table->decimal('accommodation_amount', 15, 2)->nullable();
            $table->text('meals')->nullable();
            $table->foreignId('meal_plan_id')->nullable()->constrained('meal_plans')->nullOnDelete();
            $table->decimal('meal_plan_amount', 15, 2)->nullable();
            $table->string('featured_image')->nullable();
            $table->string('image_caption')->nullable();
            $table->json('gallery_images')->nullable();
            $table->string('location')->nullable();
            $table->integer('distance_km')->nullable();
            $table->decimal('adult_price', 15, 2)->nullable();
            $table->decimal('child_price', 15, 2)->nullable();
            $table->integer('driving_hours')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['tour_id', 'day_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_itineraries');
    }
};
