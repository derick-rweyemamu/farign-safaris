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
        Schema::table('accommodation_rates', function (Blueprint $table) {
            $table->dropColumn([
                'season',
                'start_date',
                'end_date',
                'price_per_adult_sharing',
                'price_per_adult_single',
                'price_per_child',
                'currency',
                'is_active',
            ]);
            $table->dropSoftDeletes();

            $table->string('room_type')->after('accommodation_id');
            $table->string('season')->after('room_type');
            $table->string('class')->after('season');
            $table->decimal('sto', 10, 2)->default(0)->after('class');
            $table->decimal('rack', 10, 2)->default(0)->after('sto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accommodation_rates', function (Blueprint $table) {
            $table->dropColumn(['room_type', 'season', 'class', 'sto', 'rack']);
            
            $table->enum('season', ['high', 'low'])->default('high');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('price_per_adult_sharing', 10, 2)->nullable();
            $table->decimal('price_per_adult_single', 10, 2)->nullable();
            $table->decimal('price_per_child', 10, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
        });
    }
};
