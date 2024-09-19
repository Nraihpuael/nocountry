<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class DeleteProductVariantImagesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'image_ids' => 'required|array',
            'image_ids.*' => 'required|integer|exists:product_variant_images,id',
        ];
    }

    public function messages()
    {
        return [
            'image_ids.required' => 'Debes proporcionar al menos una imagen para eliminar.',
            'image_ids.array' => 'El formato de los IDs de imágenes no es válido.',
            'image_ids.*.required' => 'El ID de cada imagen es requerido.',
            'image_ids.*.integer' => 'El ID de la imagen debe ser un número entero.',
            'image_ids.*.exists' => 'El ID de la imagen seleccionada no existe.',
        ];
    }
}
