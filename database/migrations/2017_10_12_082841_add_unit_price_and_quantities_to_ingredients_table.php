<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitPriceAndQuantitiesToIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('custom_unit_price_small', 13, 2)->default(0)->after('unit_price');
            $table->decimal('custom_unit_price_medium', 13, 2)->default(0)->after('custom_unit_price_small');
            $table->decimal('custom_unit_price_large', 13, 2)->default(0)->after('custom_unit_price_medium');
            $table->unsignedInteger('custom_quantity_needed_small')->default(0)->after('custom_unit_price_large');
            $table->unsignedInteger('custom_quantity_needed_medium')->default(0)->after('custom_quantity_needed_small');
            $table->unsignedInteger('custom_quantity_needed_large')->default(0)->after('custom_quantity_needed_medium');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn([
                'custom_unit_price_small',
                'custom_unit_price_medium',
                'custom_unit_price_large',
                'custom_quantity_needed_small',
                'custom_quantity_needed_medium',
                'custom_quantity_needed_large',
            ]);
        });
    }
}
