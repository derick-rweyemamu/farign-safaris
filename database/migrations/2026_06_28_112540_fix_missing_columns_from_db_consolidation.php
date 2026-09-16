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
        Schema::table('posts', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('slug');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->unsignedBigInteger('author_id')->nullable()->after('published_at');
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->text('child_policy')->nullable()->after('min_guests');
        });

        Schema::table('tour_itineraries', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('meals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'published_at', 'author_id']);
        });

        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('child_policy');
        });

        Schema::table('tour_itineraries', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
