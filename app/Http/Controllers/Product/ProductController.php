<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Models\Product\Product;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Store\Store;
use App\Traits\PaginationAndSorting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use PaginationAndSorting;


    /**
     * Listar todos los productos (público).
     */
    public function index(Request $request)
    {
        try {
            $products = $this->applyPaginationAndSorting(
                Product::with('category', 'store'),
                $request
            );

            return ResponseHelper::success('Productos obtenidos correctamente', $products);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener los productos', 500);
        }
    }

    /**
     * Listar todos los productos de una tienda (vendedor autenticado).
     */
    public function storeProducts(Request $request, $storeId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $query = Product::where('store_id', $storeId);

            $products = $this->applyPaginationAndSorting($query, $request);

            return ResponseHelper::success('Productos de la tienda obtenidos correctamente', $products, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener los productos de la tienda: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Mostrar un producto específico (público).
     */
    public function show($id)
    {
        try {
            $product = Product::with('category', 'store')->findOrFail($id);

            return ResponseHelper::success('Producto obtenido correctamente', $product);
        } catch (\Exception $e) {
            return ResponseHelper::error('Producto no encontrado', 404);
        }
    }

    /**
     * Mostrar un producto específico para un vendedor autenticado.
     */
    public function showProduct($storeId, $productId)
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

            return ResponseHelper::success('Producto obtenido correctamente', $product, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener el producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Crear un nuevo producto para un vendedor autenticado.
     */
    public function storeProduct(StoreProductRequest $request, $storeId)
    {
        try {
            $store = Store::where(['id' => $storeId, 'user_id' => Auth::id()])->first();
            if (!$store) {
                return ResponseHelper::error('Permiso denegado', 403);
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'tax' => $request->tax,
                'store_id' => $storeId,
                'category_id' => $request->category_id,
            ]);

            return ResponseHelper::success('Producto creado correctamente', $product, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al crear el producto: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Actualizar un producto de un vendedor autenticado.
     */
    public function updateProduct(UpdateProductRequest $request, $storeId, $productId)
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

            $product->update($request->validated());

            return ResponseHelper::success('Producto actualizado correctamente', $product, 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al actualizar el producto: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Eliminar un producto de un vendedor autenticado.
     */
    public function destroyProduct($storeId, $productId)
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

            $product->delete();

            return ResponseHelper::success('Producto eliminado correctamente', [], 200);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al eliminar el producto: ' . $e->getMessage(), 500);
        }
    }

}
