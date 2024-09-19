<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UploadProductVariantImagesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'images.required' => 'Debes proporcionar al menos una imagen para subir.',
            'images.array' => 'El formato de las imágenes no es válido.',
            'images.*.required' => 'Cada archivo de imagen es requerido.',
            'images.*.image' => 'Solo se permiten archivos de imagen.',
            'images.*.mimes' => 'Las imágenes deben ser de tipo: jpeg, png, jpg.',
            'images.*.max' => 'El tamaño máximo permitido para cada imagen es 2MB.',
        ];
    }
}
