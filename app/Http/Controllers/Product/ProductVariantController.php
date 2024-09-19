<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Product\StoreProductVariantRequest;
use App\Http\Requests\Product\UpdateProductVariantRequest;
use App\Models\Product\Product;
use App\Models\Product\ProductVariant;
use App\Models\Store\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductVariantController extends Controller
{

    /**
     * Listar todas las variaciones de un producto (público).
     */
    public function publicIndex($productId)
    {
        try {
            $product = Product::with([
                'variants' => function ($query) {
                    $query->where('is_active', true);
                }
            ])->findOrFail($productId);

            return ResponseHelper::success('Variaciones obtenidas correctamente', $product->variants);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener las variaciones', 500);
        }
    }


    /**
     * Mostrar una variación específica de un producto (público).
     */
    public function publicShow($productId, $variantId)
    {
        try {
            $variant = ProductVariant::where([
                'id' => $variantId,
                'product_id' => $productId,
                'is_active' => true
            ])->firstOrFail();

            return ResponseHelper::success('Variación obtenida correctamente', $variant);
        } catch (\Exception $e) {
            return ResponseHelper::error('Variación no encontrada', 404);
        }
    }


    /**
     * Listar todas las variaciones de un producto.
     */
    public function index($storeId, $productId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $product = Product::where(['id' => $productId, 'store_id' => $storeId])->first();
            if (!$product) {
                return ResponseHelper::error('Producto no encontrado', 404);
            }

            $variants = ProductVariant::where('product_id', $productId)->get();

            return ResponseHelper::success('Variaciones obtenidas correctamente', $variants);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener las variaciones: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Obtener una variación específica de un producto.
     */
    public function show($storeId, $productId, $variantId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $product = Product::where(['id' => $productId, 'store_id' => $storeId])->first();
            $variant = ProductVariant::where(['id' => $variantId, 'product_id' => $productId])->first();
            if (!$product || !$variant) {
                return ResponseHelper::error('Producto o variación no encontrado', 404);
            }

            return ResponseHelper::success('Variación obtenida correctamente', $variant);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener la variación: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Crear una variación de producto.
     */
    public function store(StoreProductVariantRequest $request, $storeId, $productId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $product = Product::where(['id' => $productId, 'store_id' => $storeId])->first();
            if (!$product) {
                return ResponseHelper::error('Producto no encontrado', 404);
            }

            $variantData = $request->validated();
            $variantData['product_id'] = $productId;

            $variant = ProductVariant::create($variantData);

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store(
                    'store/' . $store->uuid . '/products/' . $product->id . '/variations/' . $variant->id,
                    'public'
                );
                $variant->image_url = $imagePath;
                $variant->save();
            }

            return ResponseHelper::success('Variación creada correctamente', $variant, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al crear la variación: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Actualizar una variación de producto.
     */
    public function update(UpdateProductVariantRequest $request, $storeId, $productId, $variantId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $product = Product::where(['id' => $productId, 'store_id' => $storeId])->first();
            $variant = ProductVariant::where(['id' => $variantId, 'product_id' => $productId])->first();
            if (!$product || !$variant) {
                return ResponseHelper::error('Producto o variación no encontrado', 404);
            }

            $variantData = $request->validated();

            if ($request->hasFile('image')) {
                if ($variant->image_url && Storage::disk('public')->exists($variant->image_url)) {
                    Storage::disk('public')->delete($variant->image_url);
                }

                $imagePath = $request->file('image')->store(
                    'store/' . $store->uuid . '/products/' . $product->id . '/variations/' . $variant->id,
                    'public'
                );
                $variantData['image_url'] = $imagePath;
            }

            $variant->update($variantData);

            return ResponseHelper::success('Variación actualizada correctamente', $variant, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al actualizar la variación: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar una variación de producto.
     */
    public function destroy($storeId, $productId, $variantId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $product = Product::where(['id' => $productId, 'store_id' => $storeId])->first();
            $variant = ProductVariant::where(['id' => $variantId, 'product_id' => $productId])->first();
            if (!$product || !$variant) {
                return ResponseHelper::error('Producto o variación no encontrado', 404);
            }

            $variantFolderPath = 'store/' . $store->uuid . '/products/' . $product->id . '/variations/' . $variant->id;

            if (Storage::disk('public')->exists($variantFolderPath)) {
                Storage::disk('public')->deleteDirectory($variantFolderPath);
            }

            $variant->delete();

            return ResponseHelper::success('Variación y sus archivos asociados eliminados correctamente', [], 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al eliminar la variación: ' . $e->getMessage(), 500);
        }
    }
}
