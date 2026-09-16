<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: clients table + make user_id and address_line1 nullable
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('salutation')->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('restrict');

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('tin')->nullable();
            $table->string('vrn')->nullable();

            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_country')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            $table->json('preferences')->nullable();
            $table->json('dietary_restrictions')->nullable();
            $table->json('medical_conditions')->nullable();

            $table->string('passport_number')->nullable()->unique();
            $table->date('passport_expiry_date')->nullable();
            $table->string('passport_country')->nullable();

            $table->string('loyalty_membership_number')->nullable()->unique();
            $table->decimal('loyalty_points', 10, 2)->default(0);
            $table->enum('membership_tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');

            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('active');
            $table->text('notes')->nullable();

            $table->index('name');
            $table->index('phone');
            $table->index('city_id');
            $table->index('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
