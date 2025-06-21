<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProducRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock_qty' => 'required|integer|min:0',
            'brand_id' => 'sometimes|exists:brand,brand_id',
            'category_id' => 'sometimes|exists:categories,category_id',
            'image' => 'sometimes|string|max:255',
            'expiry_date' => 'sometimes|date',
            'status' =>'required|integer' //1 is available 0 is out_of_stock	
        ];
    }
}
