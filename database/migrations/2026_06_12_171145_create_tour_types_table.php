<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default types
        DB::table('tour_types')->insert([
            [
                'name' => 'Safari',
                'slug' => 'safari',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hiking',
                'slug' => 'hiking',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Schema::table('tours', function (Blueprint $table) {
            $table->foreignId('tour_type_id')->nullable()->after('tour_category_id')->constrained('tour_types')->nullOnDelete();
        });

        // Map existing string types to the new tour_type_id
        $safariId = DB::table('tour_types')->where('name', 'Safari')->value('id');
        $hikingId = DB::table('tour_types')->where('name', 'Hiking')->value('id');

        if ($safariId) {
            DB::table('tours')->where('type', 'Safari')->update(['tour_type_id' => $safariId]);
        }
        if ($hikingId) {
            DB::table('tours')->where('type', 'Hiking')->update(['tour_type_id' => $hikingId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropForeign(['tour_type_id']);
            $table->dropColumn('tour_type_id');
        });

        Schema::dropIfExists('tour_types');
    }
};
