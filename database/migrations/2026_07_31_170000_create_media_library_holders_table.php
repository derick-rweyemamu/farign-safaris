<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_library_holders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('General Media Library');
            $table->timestamps();
        });

        // Seed default singleton row
        DB::table('media_library_holders')->insert([
            'id' => 1,
            'name' => 'General Media Library',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('media_library_holders');
    }
};
