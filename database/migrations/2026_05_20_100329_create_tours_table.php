<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: tours + tour_tags + tour_tour_tag + pax_prices
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_category_id')->nullable()->constrained('tour_categories')->nullOnDelete();
            $table->string('type')->default('Safari')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('overview')->nullable();
            $table->json('included_features');
            $table->json('excluded_features');
            $table->integer('min_age')->default(1);
            $table->integer('max_age')->nullable();
            $table->boolean('accepts_children')->default(true);
            $table->integer('children_free_under_age')->default(5);
            $table->boolean('accepts_solo_travelers')->default(false);
            $table->string('featured_image')->nullable();
            $table->string('duration_days')->nullable(); // e.g., "5 Days"
            $table->integer('duration_nights')->nullable();
            $table->string('difficulty')->nullable();
            $table->json('seasonal_months')->nullable();
            $table->integer('max_guests')->nullable();
            $table->integer('min_guests')->default(1);
            $table->json('gallery')->nullable();
            $table->decimal('adult_price', 10, 2)->nullable();
            $table->decimal('price_2_pax', 10, 2)->nullable();
            $table->decimal('price_3_pax', 10, 2)->nullable();
            $table->decimal('price_4_pax', 10, 2)->nullable();
            $table->decimal('price_5_pax', 10, 2)->nullable();
            $table->decimal('price_6_pax', 10, 2)->nullable();
            $table->decimal('price_7_plus_pax', 10, 2)->nullable();
            $table->decimal('child_price', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tour_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tour_tour_tag', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_tag_id')->constrained('tour_tags')->cascadeOnDelete();
            $table->primary(['tour_id', 'tour_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_tour_tag');
        Schema::dropIfExists('tour_tags');
        Schema::dropIfExists('tours');
    }
};
