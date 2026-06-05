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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique(); // Example: 'cyberpunk-2077' for clean URLs
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->default(0.00); // Supports prices up to 999,999.99
            $table->string('download_url')->nullable(); // Where the game file (.zip/.exe) is stored
            $table->string('cover_image')->nullable(); // To display a cool thumbnail on the store
            $table->timestamps(); // Generates created_at and updated_at automatically
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};