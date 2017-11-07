<?php

namespace App\Helpers;

use App\IngredientCategory;
use Form;

class PizzaHelper
{
    public static function ingredientCategoryDropdown($name = 'ingredient_category_id', $label = 'Ingredient Category', $default = false, $attrs = [])
    {
        $options = IngredientCategory::toList()->prepend('** CHOOSE AN INGREDIENT CATEGORY **', '')->all();

        return Form::bsSelect($name, $label, $options, $default, $attrs);
    }

    public static function categorizedIngredients()
    {
        return IngredientCategory::select('id', 'description')
            ->alphabetized()
            ->with(['ingredients' => function ($q) {
                $q->select('id', 'description', 'unit_price', 'ingredient_category_id', 'custom_quantity_needed_small', 'custom_quantity_needed_medium', 'custom_quantity_needed_large')->alphabetized();
            }])
            ->get();
    }

    public static function sizes()
    {
        return collect(['SMALL', 'MEDIUM', 'LARGE']);
    }
}
