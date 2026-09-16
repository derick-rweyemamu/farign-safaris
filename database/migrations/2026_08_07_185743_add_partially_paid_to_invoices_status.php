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
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'sent', 'paid', 'unpaid', 'overdue', 'cancelled', 'refunded', 'partially_paid') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('draft', 'sent', 'paid', 'unpaid', 'overdue', 'cancelled', 'refunded') NOT NULL DEFAULT 'draft'");
    }
};
