<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
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
        return [
            'customer_id' => 'required|numeric|exists:customers,id',
            'payed'       => 'nullable|boolean',
            'products' => [
                'sometimes',
                'array',
                Rule::exists('products', 'id')->whereIn('id', $this->input('products')),
            ],
        ];
    }
}
