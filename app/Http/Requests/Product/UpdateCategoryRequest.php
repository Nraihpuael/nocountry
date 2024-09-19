<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Reglas de validación para actualizar una categoría.
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255|unique:categories,name,' . $this->route('category'),
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Mensajes personalizados de error.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'El nombre de la categoría no debe exceder los 255 caracteres.',
            'name.unique' => 'El nombre de la categoría ya existe. Por favor, elija otro.',
            'description.string' => 'La descripción debe ser un texto válido.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'image.max' => 'La imagen no debe exceder los 2MB.',
        ];
    }
}
