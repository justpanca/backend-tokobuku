<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->foreign('product_id')->references('id')->on('products');
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('order_id')->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->text('address');
            $table->integer('total_price');
            $table->integer('quantity');
            $table->enum('status', ['pending', 'success', 'cancel']);
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
