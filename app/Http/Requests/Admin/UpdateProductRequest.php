<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'barcode' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'min_stock_alert' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
