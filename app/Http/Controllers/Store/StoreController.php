<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\Store\Store;
use App\Http\Requests\Store\StoreCreateRequest;
use App\Http\Requests\Store\StoreUpdateRequest;
use App\Traits\PaginationAndSorting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    use PaginationAndSorting;

    /**
     * Listar todas las tiendas.
     */
    public function index(Request $request)
    {
        $query = Store::query();

        // Aplicamos paginación y ordenamiento usando el trait.
        $stores = $this->applyPaginationAndSorting($query, $request);

        $customResponse = [
            'current_page' => $stores->currentPage(),
            'total_stores' => $stores->total(),
            'stores' => $stores->items(),
            'per_page' => $stores->perPage(),
            'last_page' => $stores->lastPage(),
            'next_page_url' => $stores->nextPageUrl(),
            'prev_page_url' => $stores->previousPageUrl(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Tiendas obtenidas correctamente',
            'data' => $customResponse,
        ], 200);
    }

    /**
     * Listar todas las tiendas del usuario autenticado.
     */
    public function sellerStores(Request $request)
    {
        $query = Store::where('user_id', Auth::id());

        $stores = $this->applyPaginationAndSorting($query, $request);

        $customResponse = [
            'current_page' => $stores->currentPage(),
            'total_stores' => $stores->total(),
            'stores' => $stores->items(),
            'per_page' => $stores->perPage(),
            'last_page' => $stores->lastPage(),
            'next_page_url' => $stores->nextPageUrl(),
            'prev_page_url' => $stores->previousPageUrl(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Tiendas del usuario obtenidas correctamente',
            'data' => $customResponse,
        ], 200);
    }


    /**
     * Mostrar una tienda específica.
     */
    public function show($id)
    {
        $store = Store::find($id);

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tienda obtenida exitosamente',
            'data' => $store,
        ], 200);
    }

    /**
     * Mostrar una tienda específica del usuario autenticado.
     */
    public function showSellerStore($id)
    {
        $store = Store::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada o no tienes permiso para acceder a esta tienda',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tienda obtenida exitosamente',
            'data' => $store,
        ], 200);
    }


    /**
     * Crear una nueva tienda para el usuario autenticado.
     */
    public function storeSellerStore(StoreCreateRequest $request)
    {
        $uuid = Str::uuid();
    
        $store = Store::create([
            'uuid' => $uuid,
            'name' => $request->name,
            'description' => $request->description,
            'url' => $request->url,
            'physical_address' => $request->physical_address,
            'user_id' => Auth::id(), 
        ]);
    
        if ($request->hasFile('cover_image')) {
            $imagePath = $request->file('cover_image')->store('store/' . $store->uuid . '/cover_image', 'public');
            $store->update(['cover_image' => $imagePath]);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Tienda creada correctamente',
            'data' => $store,
        ], 201);
    }
    

    /**
     * Actualizar una tienda del usuario autenticado.
     */
    public function updateSellerStore(StoreUpdateRequest $request, $id)
    {
        $store = Store::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Tienda no encontrada o no tienes permiso para editar a esta tienda',
            ], 403);
        }

        $validatedData = $request->except('cover_image');

        if ($request->hasFile('cover_image')) {
            if ($store->cover_image && Storage::disk('public')->exists($store->cover_image)) {
                Storage::disk('public')->delete($store->cover_image);
            }

            $imagePath = $request->file('cover_image')->store('store/' . $store->uuid . '/cover_image', 'public');
            $validatedData['cover_image'] = $imagePath;
        }

        $store->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tienda actualizada correctamente',
            'data' => $store,
        ], 200);
    }




    /**
     * Eliminar una tienda del usuario autenticado.
     */
    public function destroySellerStore($id)
    {
        $store = Store::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta tienda o la tienda no existe',
            ], 403);
        }

        $storeFolder = 'store/' . $store->uuid;
        if (Storage::disk('public')->exists($storeFolder)) {
            Storage::disk('public')->deleteDirectory($storeFolder);
        }

        $store->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tienda y archivos asociados eliminados correctamente',
        ], 200);
    }

}
