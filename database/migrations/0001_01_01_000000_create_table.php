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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('address')->nullable();
            $table->enum('role', ['admin', 'owner', 'costumer']);
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Products Table
        Schema::create('products', function (Blueprint $table) {
            $table->id('id_product');
            $table->foreignId('id_category')->constrained('categories', 'id_category')->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->foreignId('id_user')->constrained('users', 'id')->onDelete('cascade');
            $table->dateTime('order_date');
            $table->enum('status', ['pending', 'processed', 'shipped', 'delivered', 'cancelled']);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });

        // Order Details Table
        Schema::create('order_details', function (Blueprint $table) {
            $table->id('id_order_detail');
            $table->foreignId('id_order')->constrained('orders', 'id_order')->onDelete('cascade');
            $table->foreignId('id_product')->constrained('products', 'id_product')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
        });

        // Transactions Table
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('id_transaction');
            $table->foreignId('id_order')->constrained('orders', 'id_order')->onDelete('cascade');
            $table->enum('payment_method', ['bank_transfer', 'e-wallet']);
            $table->enum('payment_status', ['pending', 'paid', 'failed']);
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
