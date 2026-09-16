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
        Schema::create('profit_losses', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_general_expenses', 15, 2)->default(0);
            $table->decimal('total_invoice_expenses', 15, 2)->default(0);
            $table->decimal('total_payroll', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            $table->unique(['month', 'year']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profit_losses');
    }
};
