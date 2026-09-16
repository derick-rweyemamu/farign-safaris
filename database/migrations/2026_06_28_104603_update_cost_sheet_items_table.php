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
        Schema::table('cost_sheet_items', function (Blueprint $table) {
            $table->renameColumn('category', 'item_type');
            $table->renameColumn('item_name', 'name');
            $table->renameColumn('description', 'notes');
            $table->renameColumn('unit_price', 'unit_cost');
            $table->renameColumn('total_price', 'line_total');
            $table->renameColumn('reference_id', 'item_id');
        });

        Schema::table('cost_sheet_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_guests', 'quantity_days', 'reference_type']);
            $table->string('rate_label')->nullable()->after('name');
            $table->integer('quantity')->default(1)->after('unit_cost');
            $table->integer('sort_order')->default(0)->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_sheet_items', function (Blueprint $table) {
            $table->renameColumn('item_type', 'category');
            $table->renameColumn('name', 'item_name');
            $table->renameColumn('notes', 'description');
            $table->renameColumn('unit_cost', 'unit_price');
            $table->renameColumn('line_total', 'total_price');
            $table->renameColumn('item_id', 'reference_id');
        });

        Schema::table('cost_sheet_items', function (Blueprint $table) {
            $table->dropColumn(['rate_label', 'quantity', 'sort_order']);
            $table->integer('quantity_guests')->default(1)->after('unit_price');
            $table->integer('quantity_days')->default(1)->after('quantity_guests');
            $table->string('reference_type')->nullable()->after('reference_id');
        });
    }
};
