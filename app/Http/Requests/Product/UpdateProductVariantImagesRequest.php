<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantImagesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'images' => 'required|array',
            'images.*.id' => 'required|exists:product_variant_images,id',
            'images.*.file' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'images.required' => 'Debes proporcionar las imágenes para actualizar.',
            'images.array' => 'El formato de las imágenes no es válido.',
            'images.*.id.required' => 'El ID de la imagen es requerido.',
            'images.*.id.exists' => 'El ID de la imagen seleccionada no existe.',
            'images.*.file.required' => 'Es necesario que adjuntes un archivo de imagen.',
            'images.*.file.image' => 'Cada archivo debe ser una imagen válida.',
            'images.*.file.mimes' => 'Las imágenes deben ser de tipo: jpeg, png, jpg.',
            'images.*.file.max' => 'El tamaño máximo permitido para cada imagen es 2MB.',
        ];
    }
}
