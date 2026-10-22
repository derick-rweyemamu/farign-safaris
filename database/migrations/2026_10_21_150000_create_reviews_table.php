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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 50)->default('tripadvisor'); // tripadvisor, google, safari_bookings, direct
            $table->string('author_name');
            $table->string('author_avatar')->nullable();
            $table->string('author_location')->nullable();
            $table->decimal('rating', 2, 1)->default(5.0);
            $table->string('title')->nullable();
            $table->text('content');
            $table->date('review_date')->nullable();
            $table->string('trip_type')->nullable(); // e.g. Safari, Kilimanjaro, Couples, Family
            $table->text('review_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('source_id')->nullable()->index();
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['platform', 'is_published']);
            $table->index(['is_featured', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
