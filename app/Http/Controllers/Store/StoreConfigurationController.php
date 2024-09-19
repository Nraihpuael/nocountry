<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\Store;
use App\Models\Store\StoreConfiguration;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


class StoreConfigurationController extends Controller
{
    /**
     * Mostrar la configuración de una tienda.
     */
    public function show($id)
    {
        try {
            $configuration = StoreConfiguration::where('store_id', $id)->firstOrFail();

            return response()->json([
                'success' => true,
                'message' => 'Configuración de la tienda obtenida correctamente',
                'data' => $configuration
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la configuración para esta tienda o ha ocurrido un error.',
            ], 404);
        }
    }


    /**
     * Mostrar la configuración de una tienda para un usuario autenticado.
     */
    public function showSellerStoreConfiguration($storeId)
    {
        $store = Store::where('id', $storeId)->where('user_id', Auth::id())->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró la tienda o no tienes permiso para acceder a esta tienda.',
            ], 404);
        }

        $configuration = StoreConfiguration::where('store_id', $storeId)->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Configuración de la tienda obtenida correctamente.',
            'data' => $configuration
        ], 200);
    }


    /**
     * Actualizar la configuración de una tienda para un usuario autenticado.
     */
    public function updateSellerStoreConfiguration(Request $request, $storeId)
{
    $store = Store::where('id', $storeId)->where('user_id', Auth::id())->first();

    if (!$store) {
        return response()->json([
            'success' => false,
            'message' => 'No se encontró la tienda o no tienes permiso para actualizar esta tienda.',
        ], 404);
    }

    $configuration = StoreConfiguration::firstOrCreate(
        ['store_id' => $storeId], 
        [ 
            'primary_color' => '#ffffff',
            'secondary_color' => '#000000',
            'background_color' => '#ffffff',
        ]
    );

    // Validar los datos recibidos
    $request->validate([
        'primary_color' => 'string',
        'secondary_color' => 'string',
        'background_color' => 'string',
    ]);

    // Actualizar la configuración
    $configuration->update($request->all());

    return response()->json([
        'success' => true,
        'message' => 'Configuración de la tienda actualizada correctamente.',
        'data' => $configuration
    ], 200);
}


}
