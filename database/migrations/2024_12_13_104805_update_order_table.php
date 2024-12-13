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
        //
        Schema::table('orders', function (Blueprint $table) {
            // $table->foreignId('id_product')->after('id_user')->constrained('products', 'id_product')->onDelete('cascade');
            $table->integer('quantity')->after('id_product');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
