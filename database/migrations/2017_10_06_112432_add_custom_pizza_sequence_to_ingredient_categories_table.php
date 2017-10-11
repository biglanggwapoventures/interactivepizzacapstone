<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomPizzaSequenceToIngredientCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ingredient_categories', function (Blueprint $table) {
            $table->string('custom_pizza_sequence', 2)->unique()->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ingredient_categories', function (Blueprint $table) {
            $table->dropColumn('custom_pizza_sequence');
        });
    }
}
