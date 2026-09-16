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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 10)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('base_amount', 15, 2)->default(0.00);
            $table->date('expense_date');
            $table->string('reference_no')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
