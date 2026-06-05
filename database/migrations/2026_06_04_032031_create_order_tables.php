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
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('role')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('address');
            $table->string('phone');
            $table->string('gender');
            $table->date('dob');
            $table->string('password');
            $table->string('image')->nullable();
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->string('stripe_session_id')->nullable();
            $table->string('payment_status');
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('orderItem', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('item')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('shippingAddress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('order')->onDelete('cascade');
            $table->string('receiver_name');
            $table->string('phone_number');
            $table->string('address_line');
            $table->string('email');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippingAddress');
        Schema::dropIfExists('orderItem');
        Schema::dropIfExists('order');
        Schema::dropIfExists('customer');
    }
};