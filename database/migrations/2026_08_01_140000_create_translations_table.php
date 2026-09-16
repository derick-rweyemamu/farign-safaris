<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->id();
                $table->string('lang', 10)->index();
                $table->string('lang_key', 250);
                $table->text('lang_value')->nullable();
                $table->timestamps();

                $table->index(['lang', 'lang_key'], 'translations_lang_key_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
