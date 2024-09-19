<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'string|max:255',
            'url' => 'string|unique:stores,url,' . $this->route('id'), 
            'user_id' => 'exists:users,id',
            'description' => 'nullable|string|max:1000',
            'physical_address' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];
    }

    /**
     * Mensajes de error personalizados.
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'url.required' => 'La URL es obligatoria.',
            'url.unique' => 'La URL ya está en uso.',
            'user_id.required' => 'El ID de usuario es obligatorio.',
            'user_id.exists' => 'El ID de usuario no es válido.',
            'description.max' => 'La descripción no debe superar los 1000 caracteres.',
            'physical_address.max' => 'La dirección física no debe superar los 500 caracteres.',
            'cover_image.image' => 'El archivo debe ser una imagen.',
            'cover_image.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
            'cover_image.max' => 'La imagen no debe superar los 2MB.',
        ];
    }
}
