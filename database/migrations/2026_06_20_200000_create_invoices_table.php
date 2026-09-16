<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: invoices + invoice_items + invoice_expenses with all add_* patches merged
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained('estimations')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('invoices')->nullOnDelete();

            $table->date('created_date');
            $table->date('due_date')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('balance_due', 15, 2)->default(0);

            $table->string('currency_code', 10)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->decimal('exchange_rate', 15, 6)->default(1.000000);
            $table->decimal('base_grand_total', 15, 2)->default(0.00);

            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            $table->enum('status', ['draft', 'sent', 'paid', 'unpaid', 'overdue', 'cancelled', 'refunded'])->default('draft');
            $table->integer('tax_rate_index')->nullable();
            $table->integer('discount_index')->nullable();

            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_frequency')->nullable();
            $table->date('recurring_next_date')->nullable();
            $table->date('recurring_end_date')->nullable();

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('item_name')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->string('discount_type')->default('value');
            $table->boolean('tax_applied')->default(true);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('expense_type_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->string('currency_code', 10)->default('USD');
            $table->string('currency_symbol', 10)->default('$');
            $table->decimal('exchange_rate', 15, 6)->default(1.0);
            $table->decimal('base_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_expenses');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
