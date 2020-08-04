<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductGetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
        //return (auth()->guard('api')->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filter.id' => 'integer|between:0,18446744073709551615',
            'filter.created_at' => 'date',
            'filter.updated_at' => 'date',
            'filter.products\.id' => 'integer|between:0,18446744073709551615',
            'filter.products\.name' => 'string|min:2|max:255',
            'filter.products\.description' => 'string|min:2|max:255',
            'filter.products\.stock' => 'integer|between:0,18446744073709551615',
            'filter.products\.category_id' => 'integer|between:0,18446744073709551615',
            'filter.products\.created_at' => 'date',
            'filter.products\.updated_at' => 'date',
            'page.number' => 'integer|min:1',
            'page.size' => 'integer|between:1,100',
            'search' => 'nullable|string|min:3|max:60',
        ];
    }
}
