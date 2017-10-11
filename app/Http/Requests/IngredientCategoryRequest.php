<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'description' => ['required', 'max:200'],
            'custom_pizza_sequence' => ['required', 'integer', 'min:1'],
        ];
        // dd(($this->method());

        if ($this->isMethod('patch')) {
            $rules['description'][] = Rule::unique('ingredient_categories')->ignore($this->route('ingredient_category'));
            $rules['custom_pizza_sequence'][] = Rule::unique('ingredient_categories')->ignore($this->route('ingredient_category'));
        } else {
            $rules['description'][] = Rule::unique('ingredient_categories');
        }

        return $rules;
    }
}
