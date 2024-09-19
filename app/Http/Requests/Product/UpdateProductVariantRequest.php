<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'price' => 'nullable|numeric',  
            'quantity' => 'nullable|integer',  
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'  
        ];
    }

    public function messages(): array
    {
        return [
            'size.max' => 'El tamaño no debe exceder los 50 caracteres.',
            'sice.string' => 'El tamaño debe ser un texto válido.',
            'color.max' => 'El color no debe exceder los 50 caracteres.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'image.image' => 'La imagen debe ser de tipo JPEG, PNG o JPG.',
            'image.max' => 'La imagen no debe exceder los 2MB.',
        ];
    }
}
