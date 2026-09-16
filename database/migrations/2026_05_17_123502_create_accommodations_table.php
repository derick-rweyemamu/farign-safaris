<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: accommodations + accommodation_rates + season
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accommodations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->nullable(); // Lodge, Camp, Hotel, etc.
            $table->string('level')->nullable(); // Luxury, Mid-range, Budget
            $table->text('description')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('accommodation_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accommodation_id')->constrained()->cascadeOnDelete();
            $table->enum('season', ['high', 'low'])->default('high');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('price_per_adult_sharing', 10, 2)->nullable();
            $table->decimal('price_per_adult_single', 10, 2)->nullable();
            $table->decimal('price_per_child', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accommodation_rates');
        Schema::dropIfExists('accommodations');
    }
};
