<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'tax' => 'nullable|numeric',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'El nombre del producto no debe exceder los 255 caracteres.',
            'name.string' => 'El nombre del producto debe ser un texto válido.',
            'description.string' => 'La descripción debe ser un texto válido.',
            'tax.numeric' => 'El impuesto debe ser un valor numérico.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }
}
