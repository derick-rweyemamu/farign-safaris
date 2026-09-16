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
        Schema::table('cost_sheets', function (Blueprint $table) {
            $table->renameColumn('snap_concession_fee_high', 'snap_concession_fee_adult_high');
            $table->renameColumn('snap_concession_fee_low', 'snap_concession_fee_adult_low');
            $table->renameColumn('snap_crater_fee_high', 'snap_crater_fee_adult_high');
            $table->renameColumn('snap_crater_fee_low', 'snap_crater_fee_adult_low');
            $table->renameColumn('snap_park_fee_high', 'snap_park_fee_adult_high');
            $table->renameColumn('snap_park_fee_low', 'snap_park_fee_adult_low');
            $table->renameColumn('snap_pass_fee_high', 'snap_pass_fee_adult_high');
            $table->renameColumn('snap_pass_fee_low', 'snap_pass_fee_adult_low');
        });

        Schema::table('cost_sheets', function (Blueprint $table) {
            $table->decimal('snap_concession_fee_child_high', 10, 2)->default(0.00)->after('snap_concession_fee_adult_low');
            $table->decimal('snap_concession_fee_child_low', 10, 2)->default(0.00)->after('snap_concession_fee_child_high');
            $table->decimal('snap_crater_fee_child_high', 10, 2)->default(0.00)->after('snap_crater_fee_adult_low');
            $table->decimal('snap_crater_fee_child_low', 10, 2)->default(0.00)->after('snap_crater_fee_child_high');
            $table->decimal('snap_park_fee_child_high', 10, 2)->default(0.00)->after('snap_park_fee_adult_low');
            $table->decimal('snap_park_fee_child_low', 10, 2)->default(0.00)->after('snap_park_fee_child_high');
            $table->decimal('snap_pass_fee_child_high', 10, 2)->default(0.00)->after('snap_pass_fee_adult_low');
            $table->decimal('snap_pass_fee_child_low', 10, 2)->default(0.00)->after('snap_pass_fee_child_high');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cost_sheets', function (Blueprint $table) {
            $table->renameColumn('snap_concession_fee_adult_high', 'snap_concession_fee_high');
            $table->renameColumn('snap_concession_fee_adult_low', 'snap_concession_fee_low');
            $table->renameColumn('snap_crater_fee_adult_high', 'snap_crater_fee_high');
            $table->renameColumn('snap_crater_fee_adult_low', 'snap_crater_fee_low');
            $table->renameColumn('snap_park_fee_adult_high', 'snap_park_fee_high');
            $table->renameColumn('snap_park_fee_adult_low', 'snap_park_fee_low');
            $table->renameColumn('snap_pass_fee_adult_high', 'snap_pass_fee_high');
            $table->renameColumn('snap_pass_fee_adult_low', 'snap_pass_fee_low');

            $table->dropColumn([
                'snap_concession_fee_child_high',
                'snap_concession_fee_child_low',
                'snap_crater_fee_child_high',
                'snap_crater_fee_child_low',
                'snap_park_fee_child_high',
                'snap_park_fee_child_low',
                'snap_pass_fee_child_high',
                'snap_pass_fee_child_low'
            ]);
        });
    }
};
