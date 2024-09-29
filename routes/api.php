<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\MainCategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StoreOrderController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\UtilityRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('storage-link', function () {
    Artisan::call('storage:link');
});
Route::get('optimize', function () {
    Artisan::call('optimize');
});
Route::post("register", [AuthController::class, "register"]);
Route::post("login", [AuthController::class, "login"]);
Route::post("forgot-password", [UserController::class, "forgotPassword"]);
Route::post("reset-password", [UserController::class, "resetPassword"])->name('password.reset');

Route::group([
    "middleware" => ["auth:api"]
], function(){
    Route::get("refresh", [AuthController::class, "refreshToken"]);
    Route::get("logout", [AuthController::class, "logout"]);

    // Stores
    Route::post('/stores', [StoreController::class, 'store']);
    Route::get('/stores', [StoreController::class, 'index']);
    Route::get('/stores-by-token', [StoreController::class, 'storesByToken']);
    Route::get('/stores/{id}', [StoreController::class, 'show']);
    Route::patch('/stores/{id}', [StoreController::class, 'update']);

    // User Or Employee
    Route::middleware('auth.role:admin')->get('/users', [UserController::class, 'index']);
    // Route::get('/users', [UserController::class, 'index']);
    Route::get("profile", [UserController::class, "profile"]);
    Route::patch("profile", [UserController::class, "updateProfile"]);
    Route::post('/addUser', [UserController::class, 'store']);
    Route::get('/editUser/{id}', [UserController::class, 'show']);
    Route::post('/updateUser/{id}', [UserController::class, 'update']);
    Route::delete('/deleteUser/{id}', [UserController::class, 'destroy']);

    // Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders-with-token', [OrderController::class, 'getOrdersWithToken']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}', [OrderController::class, 'update']);

    // main category
    Route::post('/main-category', [MainCategoryController::class, 'store']);
    Route::get('/main-category/{id}', [MainCategoryController::class, 'show']);
    Route::patch('/main-category/{id}', [MainCategoryController::class, 'update']);
    Route::delete('/main-category/{id}', [MainCategoryController::class, 'destroy']);


    // sub category
    Route::post('/sub-category', [SubCategoryController::class, 'store']);
    Route::get('/sub-category/{id}', [SubCategoryController::class, 'show']);
    Route::patch('/sub-category/{id}', [SubCategoryController::class, 'update']);
    Route::delete('/sub-category/{id}', [SubCategoryController::class, 'destroy']);


    // category
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::patch('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


    // Brand
    Route::post('/brands', [BrandController::class, 'store']);
    Route::get('/brands/{id}', [BrandController::class, 'show']);
    Route::patch('/brands/{id}', [BrandController::class, 'update']);
    Route::delete('/brands/{id}', [BrandController::class, 'destroy']);

    // Color
    Route::post('/colors', [ColorController::class, 'store']);
    Route::get('/colors/{id}', [ColorController::class, 'show']);
    Route::patch('/colors/{id}', [ColorController::class, 'update']);
    Route::delete('/colors/{id}', [ColorController::class, 'destroy']);

    // Size
    Route::post('/sizes', [SizeController::class, 'store']);
    Route::get('/sizes/{id}', [SizeController::class, 'show']);
    Route::patch('/sizes/{id}', [SizeController::class, 'update']);
    Route::delete('/sizes/{id}', [SizeController::class, 'destroy']);

    // Products
    Route::post('/products', [ProductController::class, 'store']);
    Route::patch('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    Route::patch('/update-variation-stock/{variation_id}',[ProductVariationController::class, 'updateStock']);

    // Product Variant
    Route::patch('/product-variant/{id}', [ProductVariantController::class, 'update']);

    // Cart items
    Route::get('/cart-items', [CartItemController::class, 'index']);
    Route::post('/cart-items', [CartItemController::class, 'store']);
    Route::patch('/cart-items/{id}', [CartItemController::class, 'update']);
    Route::delete('/cart-items/{id}', [CartItemController::class, 'destroy']);

    // Payment
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
    Route::post('/payments', [PaymentController::class, 'store']);
    Route::patch('/payments/{id}', [PaymentController::class, 'update']);

    // Store Order
    Route::get('/store-orders/{store_id}',[StoreOrderController::class,'index']);
    Route::patch('/store-orders/{id}',[StoreOrderController::class,'update']);

    // Coupons
    Route::get('/coupons/{store_id}',[CouponController::class,'index']);
    Route::post('/coupons',[CouponController::class,'store']);
    Route::post('/coupons/check',[CouponController::class,'check']);

    // Utility Request
    Route::get('/utility-requests',[UtilityRequestController::class,'index']);
    Route::post('/utility-requests',[UtilityRequestController::class,'store']);
    Route::patch('/utility-requests/{id}',[UtilityRequestController::class,'update']);

    // Uploads
    Route::post('/upload-local',[UploadController::class,'upload_local']);
    Route::get('/get-local',[UploadController::class,'get_local']);


});

// Home products
Route::get('/home/data',[HomeController::class,'index']);

// main category
Route::get('/main-category', [MainCategoryController::class, 'index']);
// sub category
Route::get('/sub-category', [SubCategoryController::class, 'index']);
// category
Route::get('/categories', [CategoryController::class, 'index']);
// brands
Route::get('/brands', [BrandController::class, 'index']);
// colors
Route::get('/colors', [ColorController::class, 'index']);
// sizes
Route::get('/sizes', [SizeController::class, 'index']);

// Products
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Product Variant
Route::get('/product-variant/{id}', [ProductVariantController::class, 'show']);




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
