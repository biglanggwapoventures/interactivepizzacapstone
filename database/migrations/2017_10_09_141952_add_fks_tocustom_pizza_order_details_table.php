<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFksTocustomPizzaOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_pizza_order_details', function (Blueprint $table) {
            $table->foreign('custom_pizza_order_id')->references('id')->on('custom_pizza_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ingredient_id')->references('id')->on('ingredients')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_pizza_order_details', function (Blueprint $table) {
            $table->dropForeign(['custom_pizza_order_id']);
            $table->dropForeign(['ingredient_id']);
        });
    }
}
