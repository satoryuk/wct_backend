<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'product_name' => 'sometimes|string|max:255',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|integer',
            'stock_qty' => 'sometimes|integer',
            'category_id' => 'sometimes|exists:categories,category_id',
            'brand_id' => 'sometimes|exists:brand,brand_id',
            'expiry_date' => 'nullable|date',
            'status' => 'sometimes|in:available,out_of_stock',
        ];
    }
}
