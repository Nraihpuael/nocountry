<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreateRequest extends FormRequest
{

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|string|unique:stores',
            'description' => 'nullable|string|max:1000', 
            'physical_address' => 'nullable|string|max:500', 
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'url.required' => 'La URL es obligatoria.',
            'url.unique' => 'La URL ya está en uso.',
            'description.max' => 'La descripción no debe superar los 1000 caracteres.',
            'physical_address.max' => 'La dirección física no debe superar los 500 caracteres.',
            'cover_image.image' => 'El archivo debe ser una imagen.',
            'cover_image.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'cover_image.max' => 'La imagen no debe superar los 2MB.',
        ];
    }
}
