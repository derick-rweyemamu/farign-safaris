<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: cost_sheets + items + matrix + nullable updates
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cost_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('itinerary_id')->nullable()->constrained('itineraries')->onDelete('set null');
            $table->string('reference_number')->unique();
            $table->string('title');
            $table->string('calculation_type')->default('standard');
            $table->foreignId('destination_id')->nullable()->constrained('destinations')->onDelete('cascade');
            $table->enum('season', ['high', 'low'])->default('high');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedSmallInteger('duration_days')->default(1);

            // Guest & Vehicle Configuration
            $table->unsignedSmallInteger('num_adults')->default(1);
            $table->unsignedSmallInteger('num_children')->default(0);
            $table->unsignedSmallInteger('num_vehicles')->default(1);

            // Fee Snapshots
            $table->decimal('snap_concession_fee_high', 10, 2)->default(0.00);
            $table->decimal('snap_concession_fee_low',  10, 2)->default(0.00);
            $table->decimal('snap_crater_fee_high',     10, 2)->default(0.00);
            $table->decimal('snap_crater_fee_low',      10, 2)->default(0.00);
            $table->decimal('snap_park_fee_high',       10, 2)->default(0.00);
            $table->decimal('snap_park_fee_low',        10, 2)->default(0.00);
            $table->decimal('snap_pass_fee_high',       10, 2)->default(0.00);
            $table->decimal('snap_pass_fee_low',        10, 2)->default(0.00);

            // Calculated Subtotals
            $table->decimal('subtotal_park_fees',        12, 2)->default(0.00);
            $table->decimal('subtotal_concession_fees',  12, 2)->default(0.00);
            $table->decimal('subtotal_crater_fees',      12, 2)->default(0.00);
            $table->decimal('subtotal_pass_fees',        12, 2)->default(0.00);
            $table->decimal('subtotal_activities',       12, 2)->default(0.00);
            $table->decimal('subtotal_transport',        12, 2)->default(0.00);
            $table->decimal('subtotal_meals',            12, 2)->default(0.00);
            $table->decimal('subtotal_accommodation',    12, 2)->default(0.00);
            $table->decimal('subtotal_custom',           12, 2)->default(0.00);
            $table->decimal('subtotal',                  12, 2)->default(0.00);

            // Markup
            $table->decimal('markup_percentage', 5, 2)->default(0.00);
            $table->decimal('markup_amount',     12, 2)->default(0.00);
            $table->decimal('grand_total',       12, 2)->default(0.00);

            // Meta
            $table->text('notes')->nullable();
            $table->json('matrix_days')->nullable();
            $table->json('matrix_rows')->nullable();
            $table->enum('status', ['draft', 'finalized'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('cost_sheet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_sheet_id')->constrained('cost_sheets')->onDelete('cascade');
            $table->string('category');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->integer('quantity_guests')->default(1);
            $table->integer('quantity_days')->default(1);
            $table->decimal('total_price', 12, 2)->default(0.00);
            $table->foreignId('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_sheet_items');
        Schema::dropIfExists('cost_sheets');
    }
};
