<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: group_tours + seat_columns
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('total_seats')->nullable();
            $table->integer('booked_seats')->default(0);
            $table->integer('available_seats')->nullable();
            $table->string('featured_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_tours');
    }
};
