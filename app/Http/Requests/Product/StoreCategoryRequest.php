<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;


class StoreCategoryRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique' => 'El nombre de la categoría ya existe.',
            'name.max' => 'El nombre de la categoría no debe exceder los 255 caracteres.',
            'description.string' => 'La descripción debe ser un texto.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'image.max' => 'La imagen no debe exceder los 2MB.',
        ];
    }
}