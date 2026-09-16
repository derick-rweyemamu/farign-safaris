<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_tour_category', function (Blueprint $table) {
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tour_category_id')->constrained('tour_categories')->cascadeOnDelete();
            $table->primary(['tour_id', 'tour_category_id']);
        });

        // Migrate existing relationships from tours.tour_category_id to the new pivot table
        $tours = DB::table('tours')->whereNotNull('tour_category_id')->get();
        foreach ($tours as $tour) {
            DB::table('tour_tour_category')->insertOrIgnore([
                'tour_id' => $tour->id,
                'tour_category_id' => $tour->tour_category_id,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_tour_category');
    }
};
