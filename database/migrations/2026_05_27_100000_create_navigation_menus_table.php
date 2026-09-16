<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Consolidated: navigation_menus + parent_id
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('location')->default('header'); // header, footer, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('navigation_menus')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_menus');
    }
};
