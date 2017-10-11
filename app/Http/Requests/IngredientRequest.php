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
            'unit_price' => 'required|numeric',
            'ingredient_category_id' => 'required|exists:ingredient_categories,id',
            'photo' => ['image', 'max:5120', 'dimensions:max_width=1024,max_height=1024'],
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
