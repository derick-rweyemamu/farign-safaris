<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories')->onDelete('restrict');
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->nullable()->default(0);
            $table->string('unit')->default('piece');
            $table->json('tax_rates')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
