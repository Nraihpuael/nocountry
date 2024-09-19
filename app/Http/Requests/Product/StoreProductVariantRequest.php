<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'  
        ];
    }

    public function messages(): array
    {
        return [
            'size.max' => 'El tamaño no debe exceder los 50 caracteres.',
            'size.string' => 'El tamaño debe ser un texto válido.',
            'color.max' => 'El color no debe exceder los 50 caracteres.',
            'color.string' => 'El color debe ser un texto válido.',
            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'image.image' => 'La imagen debe ser de tipo JPEG, PNG o JPG.',
            'image.max' => 'La imagen no debe exceder los 2MB.',
        ];
    }
}
