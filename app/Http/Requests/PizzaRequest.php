<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PizzaRequest extends FormRequest
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
            'name' => ['required', 'max:200'],
            'description' => 'required|max:500',
            'photo' => ['image', 'max:5120', 'dimensions:max_width=1024,max_height=1024'], //max: 5MB;1024x1024
        ];

        if ($this->isMethod('patch')) {
            $rules['name'][] = Rule::unique('pizzas')->ignore($this->route('pizza'));
        } else {
            $rules['name'][] = Rule::unique('pizzas');
            $rules['photo'] = array_prepend($rules['photo'], 'required');
        }

        return $rules;
    }
}
