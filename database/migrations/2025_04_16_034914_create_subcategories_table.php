<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop tables if they exist
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
        
        // Create categories table first
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('category_name');
            $table->timestamps();
        });
        
        // Then create subcategories table with foreign key
        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->string('subcategory_name');
            $table->unsignedBigInteger('category_id');
            $table->timestamps();
            
            $table->foreign('category_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
};