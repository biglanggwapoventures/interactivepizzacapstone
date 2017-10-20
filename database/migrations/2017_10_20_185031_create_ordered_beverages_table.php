<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedBeveragesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_beverages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->nullable();
            $table->unsignedInteger('beverage_id')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            // $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('beverage_id')->references('id')->on('ingredients')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordered_beverages');
    }
}
