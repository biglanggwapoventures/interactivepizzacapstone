<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePizzaIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pizza_ingredients', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pizza_size_id')->nullable();
            $table->unsignedInteger('ingredient_id')->nullable();
            $table->decimal('quantity', 13,2)->nullable();
            $table->timestamps();

            // $table->foreign('pizza_size_id')->references('id')->on('pizza_sizes')
            //     ->onDelete('restrict')->onUpdate('cascade');

            $table->foreign('ingredient_id')->references('id')->on('ingredients')
                ->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pizza_ingredients');
    }
}
