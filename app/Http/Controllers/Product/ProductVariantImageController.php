<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Product\DeleteProductVariantImagesRequest;
use App\Http\Requests\Product\UploadProductVariantImagesRequest;
use App\Http\Requests\Product\UpdateProductVariantImagesRequest;
use App\Models\Product\ProductVariant;
use App\Models\Product\ProductVariantImage;
use App\Models\Store\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductVariantImageController extends Controller
{
    /**
     * Subir imágenes de una variación de producto.
     */
    public function uploadImages(UploadProductVariantImagesRequest $request, $storeId, $productId, $variantId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $variant = ProductVariant::where(['id' => $variantId, 'product_id' => $productId])->first();
            if (!$variant) {
                return ResponseHelper::error('Variación no encontrada', 404);
            }

            $maxImagesAllowed = 5;
            $existingImagesCount = $variant->images()->count();
            $newImagesCount = count($request->file('images'));

            if (($existingImagesCount + $newImagesCount) > $maxImagesAllowed) {
                return ResponseHelper::error('Límite de imágenes excedido', 422);
            }

            $uploadedImages = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('store/' . $store->uuid . '/products/' . $productId . '/variations/' . $variantId , 'public');
                $variantImage = ProductVariantImage::create([
                    'product_variant_id' => $variantId,
                    'image_url' => $imagePath
                ]);
                $uploadedImages[] = $variantImage;
            }

            return ResponseHelper::success('Imágenes subidas correctamente', $uploadedImages, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al subir las imágenes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar imágenes específicas de una variación de producto.
     */
    public function updateSpecificImages(UpdateProductVariantImagesRequest $request, $storeId, $productId, $variantId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $variant = ProductVariant::where(['id' => $variantId, 'product_id' => $productId])->first();
            if (!$variant) {
                return ResponseHelper::error('Variación no encontrada', 404);
            }

            $updatedImages = [];
            foreach ($request->images as $imageData) {
                $image = ProductVariantImage::find($imageData['id']);
                if ($image && $image->product_variant_id == $variantId) {
                    Storage::disk('public')->delete($image->image_url);

                    $imagePath = $imageData['file']->store('store/' . $store->uuid . '/products/' . $productId . '/variations/' . $variantId, 'public');
                    $image->update(['image_url' => $imagePath]);

                    $updatedImages[] = $image;
                }
            }

            return ResponseHelper::success('Imágenes actualizadas correctamente', $updatedImages, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al actualizar las imágenes: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Eliminar múltiples imágenes de una variación de producto.
     */
    public function deleteImages(DeleteProductVariantImagesRequest $request, $storeId, $productId, $variantId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $images = ProductVariantImage::whereIn('id', $request->image_ids)
                ->where('product_variant_id', $variantId)
                ->get();
            if ($images->isEmpty()) {
                return ResponseHelper::error('Imágenes no encontradas', 404);
            }

            foreach ($images as $image) {
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                    $image->delete();
                }
            }

            return ResponseHelper::success('Imágenes eliminadas', [], 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al eliminar las imágenes', 500);
        }
    }
}
