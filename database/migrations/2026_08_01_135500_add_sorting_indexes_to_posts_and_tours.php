<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->index('published_at');
                $table->index('is_published');
            });
        }

        if (Schema::hasTable('group_tours')) {
            Schema::table('group_tours', function (Blueprint $table) {
                $table->index('start_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex(['published_at']);
                $table->dropIndex(['is_published']);
            });
        }

        if (Schema::hasTable('group_tours')) {
            Schema::table('group_tours', function (Blueprint $table) {
                $table->dropIndex(['start_date']);
            });
        }
    }
};
