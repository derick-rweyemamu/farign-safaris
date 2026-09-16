<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: itineraries + client/product/pricing/tour fields
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->string('reference_number')->unique();
            $table->string('title'); // e.g. 10 Days Tanzania Safari
            $table->string('slug')->nullable();
            $table->longText('description')->nullable();
            $table->longText('overview')->nullable();
            $table->integer('min_age')->default(0);
            $table->integer('max_age')->nullable();
            $table->boolean('accepts_children')->default(true);
            $table->integer('children_free_under_age')->default(0);
            $table->boolean('accepts_solo_travelers')->default(true);
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->nullable();
            $table->integer('duration_nights')->nullable();
            $table->string('difficulty')->nullable();
            $table->json('seasonal_months')->nullable();
            $table->integer('max_guests')->nullable();
            $table->integer('min_guests')->default(1);
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('price_per_adult', 10, 2)->default(0);
            $table->decimal('price_per_child', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->json('included')->nullable();
            $table->json('excluded')->nullable();
            $table->text('payment_terms')->nullable();
            $table->string('status')->default('draft'); // draft, sent, approved, rejected
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};
