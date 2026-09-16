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
        Schema::table('destinations', function (Blueprint $table) {
            $table->renameColumn('concession_fee_high_season', 'concession_fee_adult_high_season');
            $table->renameColumn('concession_fee_low_season', 'concession_fee_adult_low_season');
            $table->renameColumn('crater_fee_high_season', 'crater_fee_adult_high_season');
            $table->renameColumn('crater_fee_low_season', 'crater_fee_adult_low_season');
            $table->renameColumn('park_fee_high_season', 'park_fee_adult_high_season');
            $table->renameColumn('park_fee_low_season', 'park_fee_adult_low_season');
            $table->renameColumn('pass_fee_high_season', 'pass_fee_adult_high_season');
            $table->renameColumn('pass_fee_low_season', 'pass_fee_adult_low_season');

            $table->decimal('concession_fee_child_high_season', 10, 2)->default(0.00);
            $table->decimal('concession_fee_child_low_season', 10, 2)->default(0.00);
            $table->decimal('crater_fee_child_high_season', 10, 2)->default(0.00);
            $table->decimal('crater_fee_child_low_season', 10, 2)->default(0.00);
            $table->decimal('park_fee_child_high_season', 10, 2)->default(0.00);
            $table->decimal('park_fee_child_low_season', 10, 2)->default(0.00);
            $table->decimal('pass_fee_child_high_season', 10, 2)->default(0.00);
            $table->decimal('pass_fee_child_low_season', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->renameColumn('concession_fee_adult_high_season', 'concession_fee_high_season');
            $table->renameColumn('concession_fee_adult_low_season', 'concession_fee_low_season');
            $table->renameColumn('crater_fee_adult_high_season', 'crater_fee_high_season');
            $table->renameColumn('crater_fee_adult_low_season', 'crater_fee_low_season');
            $table->renameColumn('park_fee_adult_high_season', 'park_fee_high_season');
            $table->renameColumn('park_fee_adult_low_season', 'park_fee_low_season');
            $table->renameColumn('pass_fee_adult_high_season', 'pass_fee_high_season');
            $table->renameColumn('pass_fee_adult_low_season', 'pass_fee_low_season');

            $table->dropColumn([
                'concession_fee_child_high_season',
                'concession_fee_child_low_season',
                'crater_fee_child_high_season',
                'crater_fee_child_low_season',
                'park_fee_child_high_season',
                'park_fee_child_low_season',
                'pass_fee_child_high_season',
                'pass_fee_child_low_season'
            ]);
        });
    }
};
