<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: destinations with all columns from all add_* patches
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('slug')->unique();
            $table->longText('description')->nullable();
            $table->longText('builder_html')->nullable();
            $table->json('builder_data')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);

            // Park / Entry Fees
            $table->decimal('concession_fee_high_season', 10, 2)->default(0.00);
            $table->decimal('concession_fee_low_season', 10, 2)->default(0.00);
            $table->decimal('crater_fee_high_season', 10, 2)->default(0.00);
            $table->decimal('crater_fee_low_season', 10, 2)->default(0.00);
            $table->decimal('park_fee_high_season', 10, 2)->default(0.00);
            $table->decimal('park_fee_low_season', 10, 2)->default(0.00);
            $table->decimal('pass_fee_high_season', 10, 2)->default(0.00);
            $table->decimal('pass_fee_low_season', 10, 2)->default(0.00);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destinations');
    }
};
