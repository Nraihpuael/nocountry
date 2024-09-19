<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use App\Http\Requests\Product\StoreCategoryRequest;
use App\Http\Requests\Product\UpdateCategoryRequest;
use App\Models\Product\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Listar todas las categorías.
     */
    public function index()
    {
        try {
            $categories = Category::where('is_active', true)->get();

            return ResponseHelper::success('Categorías obtenidas correctamente', $categories);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al obtener las categorías', 500);
        }
    }

    /**
     * Mostrar una categoría específica.
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);

            return ResponseHelper::success('Categoría obtenida correctamente', $category);
        } catch (\Exception $e) {
            return ResponseHelper::error('Categoría no encontrada', 404);
        }
    }

    /**
     * Crear una nueva categoría con una imagen.
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('category', 'public');
            }
    
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath,
            ]);
    
            return ResponseHelper::success('Categoría creada correctamente', $category, 201);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al crear la categoría', 500);
        }
    }
    /**
     * Actualizar una categoría existente.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
    
            if ($request->hasFile('image')) {
                if ($category->image_url && Storage::disk('public')->exists($category->image_url)) {
                    Storage::disk('public')->delete($category->image_url);
                }
                $imagePath = $request->file('image')->store('category', 'public');
                $category->image_url = $imagePath;
            }
    
            $category->update($request->except('image'));
    
            return ResponseHelper::success('Categoría actualizada correctamente', $category);
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al actualizar la categoría', 500);
        }
    }

    /**
     * Eliminar una categoría.
     */
    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            if ($category->image_url && Storage::disk('public')->exists($category->image_url)) {
                Storage::disk('public')->delete($category->image_url);
            }

            $category->delete();

            return ResponseHelper::success('Categoría eliminada correctamente');
        } catch (\Exception $e) {
            return ResponseHelper::error('Error al eliminar la categoría', 500);
        }
    }
}
