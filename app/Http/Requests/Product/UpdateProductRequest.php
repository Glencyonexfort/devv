<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\CoreRequest;

class UpdateProductRequest extends CoreRequest
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
            'name' => 'required',
            'category_id' => 'required|numeric',
            'price' => 'required|numeric',
            'product_type' => 'required|in:Item,Service,Charge',
        ];
    }

    public function messages()
    {
        return [
            'category_id.numeric' => 'Product Categrory field is required',
            'product_type.in' => 'Product Type field is required'
        ];
    }
}
