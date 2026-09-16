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
        Schema::create('tour_itinerary_transport', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_itinerary_id')->constrained()->onDelete('cascade');
            $table->foreignId('transport_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itinerary_transport');
    }
};
