<?php

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductImageController;
use App\Http\Controllers\Product\ProductVariantController;
use App\Http\Controllers\Product\ProductVariantImageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Store\StoreController;
use App\Http\Controllers\Store\StoreConfigurationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
//Registro
Route::post('register', [AuthController::class, 'register'])->name('register');

// Login y logout deben ser POST
Route::post('login', [AuthController::class, 'login'])->name('login');
//Route::post('logout', [AuthController::class, 'logout'])->name('logout');

//Contraseña
Route::post('forgot-password', [ResetPasswordController::class, "forgotPassword"]);
Route::post('reset-password', [ResetPasswordController::class, "updatePassword"]);
Route::post('verify-token', [ResetPasswordController::class, "verifyToken"]);

//Verificar usuario
Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');


//Rutas publicas tienda
Route::prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index']);
    Route::get('/{store}', [StoreController::class, 'show']);
    Route::get('/{store}/configuration', [StoreConfigurationController::class, 'show']);
});

// Rutas publicas productos
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{product}', [ProductController::class, 'show']);
});

// Rutas públicas para variaciones de productos
Route::prefix('products')->group(function () {
    Route::get('/{product}/variants', [ProductVariantController::class, 'publicIndex']);
    Route::get('/{product}/variants/{variant}', [ProductVariantController::class, 'publicShow']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'show'])->name('user.show');
        Route::get('/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/update', [UserController::class, 'update'])->name('user.update');
        Route::delete('/delete', [UserController::class, 'destroy'])->name('user.destroy');
    });

    Route::prefix('sellers')->middleware(['role:seller'])->group(function () {
        Route::prefix('stores')->group(function () {
            // Tiendas del usuario autenticado
            Route::get('/', [StoreController::class, 'sellerStores']);
            Route::post('/', [StoreController::class, 'storeSellerStore']);
            Route::get('/{store}', [StoreController::class, 'showSellerStore']);
            Route::put('/{store}', [StoreController::class, 'updateSellerStore']);
            Route::delete('/{store}', [StoreController::class, 'destroySellerStore']);

            // Configuraciones de tienda para el usuario autenticado
            Route::get('/{store}/configuration', [StoreConfigurationController::class, 'showSellerStoreConfiguration']);
            Route::put('/{store}/configuration', [StoreConfigurationController::class, 'updateSellerStoreConfiguration']);

            // Rutas de Productos
            Route::prefix('/{store}/products')->group(function () {
                Route::get('/', [ProductController::class, 'storeProducts']);
                Route::post('/', [ProductController::class, 'storeProduct']);
                Route::get('/{product}', [ProductController::class, 'showProduct']);
                Route::put('/{product}', [ProductController::class, 'updateProduct']);
                Route::delete('/{product}', [ProductController::class, 'destroyProduct']);

                // Rutas de Variaciones de Productos
                Route::prefix('/{product}/variants')->group(function () {
                    Route::post('/', [ProductVariantController::class, 'store']);
                    Route::get('/', [ProductVariantController::class, 'index']);
                    Route::get('/{variant}', [ProductVariantController::class, 'show']);
                    Route::put('/{variant}', [ProductVariantController::class, 'update']);
                    Route::delete('/{variant}', [ProductVariantController::class, 'destroy']);

                    // Rutas de imágenes de variaciones
                    Route::prefix('/{variant}/images')->group(function () {
                        Route::post('/', [ProductVariantImageController::class, 'uploadImages']);
                        Route::put('/', [ProductVariantImageController::class, 'updateSpecificImages']);
                        Route::delete('/', [ProductVariantImageController::class, 'deleteImages']);
                    });

                });


            });
        });
    });

    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

});


Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);

    Route::middleware(['auth:sanctum', 'verified.api', 'role:admin'])->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });
});


