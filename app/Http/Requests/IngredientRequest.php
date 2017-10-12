<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientRequest extends FormRequest
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
            'unit_price' => 'numeric',
            'ingredient_category_id' => 'required|exists:ingredient_categories,id',
            'photo' => ['image', 'max:5120', 'dimensions:max_width=1024,max_height=1024'],
            'custom_unit_price_small' => 'required|numeric|min:0',
            'custom_unit_price_medium' => 'required|numeric|min:0',
            'custom_unit_price_large' => 'required|numeric|min:0',
            'custom_quantity_needed_small' => 'required|integer|min:1',
            'custom_quantity_needed_medium' => 'required|integer|min:1',
            'custom_quantity_needed_large' => 'required|integer|min:1',
        ];

        if ($this->isMethod('patch')) {
            $rules['description'][] = Rule::unique('ingredients')->ignore($this->route('ingredient'));
        } else {
            $rules['description'][] = Rule::unique('ingredients');
            $rules['photo'] = array_prepend($rules['photo'], 'required');
        }

        return $rules;
    }
}
