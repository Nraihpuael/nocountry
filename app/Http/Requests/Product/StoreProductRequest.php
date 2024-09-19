<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tax' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'name.max' => 'El nombre del producto no debe exceder los 255 caracteres.',
            'name.string' => 'El nombre del producto debe ser un texto válido.',
            'description.string' => 'La descripción debe ser un texto válido.',
            'description.required' => 'La descripción del producto es obligatoria.',
            'tax.numeric' => 'El impuesto debe ser un valor numérico.',
            'category_id.required' => 'La categoría del producto es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }
}
