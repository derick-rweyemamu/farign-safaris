<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: estimations + estimation_products with all add_* patches merged
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('itinerary_id')->nullable()->constrained('itineraries')->onDelete('set null');
            $table->foreignId('tour_id')->nullable()->constrained('tours')->onDelete('set null');
            $table->string('estimate_number')->unique();
            $table->date('date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected'])->default('draft');
            $table->integer('tax_rate_index')->nullable();
            $table->integer('discount_index')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->string('currency_code', 10)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('base_grand_total', 15, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('estimation_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estimation_id')->constrained('estimations')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('tour_id')->nullable()->constrained('tours')->onDelete('set null');
            $table->string('item_name')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('discount_value', 10, 2)->default(0.00);
            $table->string('discount_type')->default('value');
            $table->boolean('tax_applied')->default(true);
            $table->integer('adults')->nullable();
            $table->integer('children')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimation_products');
        Schema::dropIfExists('estimations');
    }
};
