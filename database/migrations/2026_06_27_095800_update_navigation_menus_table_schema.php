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
        Schema::table('navigation_menus', function (Blueprint $table) {
            $table->renameColumn('name', 'label');
            $table->foreignId('page_id')->nullable()->after('url')->constrained('pages')->nullOnDelete();
            $table->boolean('is_external')->default(false)->after('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('navigation_menus', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropColumn(['page_id', 'is_external']);
            $table->renameColumn('label', 'name');
        });
    }
};
