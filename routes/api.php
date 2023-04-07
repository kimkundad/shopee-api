<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => 'api'
], function ($router) {

    Route::get('/get_category_all', [App\Http\Controllers\ApiController::class, 'get_category_all']);
    Route::get('/allProduct/{id}', [App\Http\Controllers\ApiController::class, 'get_all_product']);
    Route::get('/getProduct', [App\Http\Controllers\ApiController::class, 'get_product']);
    Route::put('/setActiveProduct', [App\Http\Controllers\ApiController::class, 'set_active_product']);
    Route::put('/setActiveAllProduct', [App\Http\Controllers\ApiController::class, 'set_active_AllProduct']);
    Route::post('/deleteProduct/{id}', [App\Http\Controllers\ApiController::class, 'delete_product']);
    Route::post('/addProduct', [App\Http\Controllers\ApiController::class, 'addProduct']);
    Route::post('/addProductMultiOption', [App\Http\Controllers\ApiController::class, 'addProductMultiOption']);
    Route::get('/shop/{id}', [App\Http\Controllers\ApiController::class, 'get_shop_name']);
    Route::post('/searchProduct/{id}', [App\Http\Controllers\ApiController::class, 'search_product']);
    Route::get('/getAllProduct', [App\Http\Controllers\ApiController::class, 'get_allproduct']);
    Route::get('/getAllShops', [App\Http\Controllers\ApiController::class, 'get_all_shops']);
    Route::get('/getSearchShops', [App\Http\Controllers\ApiController::class, 'get_search_shops']);
    Route::get('/getSearchDateShops', [App\Http\Controllers\ApiController::class, 'get_search_date_shops']);
    Route::get('/getFilterShops', [App\Http\Controllers\ApiController::class, 'get_filter_shops']);
    Route::post('/addProductToCart', [App\Http\Controllers\ApiController::class, 'addProductToCart']);
    Route::get('/getAllCartItem/{id}', [App\Http\Controllers\ApiController::class, 'getAllCartItem']);
    Route::get('/getAllUsers', [App\Http\Controllers\ApiController::class, 'getAllUsers']); // route ของการดึงข้อมูล users ออกมาทั้งหมด
    Route::post('/createSubAdmin', [App\Http\Controllers\ApiController::class, 'createSubAdmin']); // route สร้างข้อมูล sub-admin ขึ้นมา
    Route::post('/updateSubAdmin', [App\Http\Controllers\ApiController::class, 'updateSubAdmin']); // route อัพเดทข้อมูล sub-admin
    Route::post('/deleteSubAdmin', [App\Http\Controllers\ApiController::class, 'deleteSubAdmin']); // route ลบข้อมูลข้อง sub-admin ออก
    Route::get('/getSearchDateSubAdmin', [App\Http\Controllers\ApiController::class, 'getSearchDateSubAdmin']); // route การทำ filter ค้นหาข้อมูล sub-admin จาก วันที่ที่สร้าง
    Route::get('/getSearchName', [App\Http\Controllers\ApiController::class, 'getSearchName']); // route การทำ filter ค้นหาข้อมูล sub-admin จากชื่อ
    Route::post('/deleteCartItem/{id}', [App\Http\Controllers\ApiController::class, 'deleteItemCart']);
    Route::post('/createShop', [App\Http\Controllers\ApiController::class, 'createShop']);
    Route::post('/editShop', [App\Http\Controllers\ApiController::class, 'editShop']);
    Route::post('/DeleteShop', [App\Http\Controllers\ApiController::class, 'DeleteShop']);
    Route::post('/changeStatusShop', [App\Http\Controllers\ApiController::class, 'changeStatusShop']);

    Route::post('/createUser', [App\Http\Controllers\AuthController::class, 'createUser']);
    Route::post('/verify', [App\Http\Controllers\AuthController::class, 'verify']);
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::get('/user-profile', [App\Http\Controllers\AuthController::class, 'userProfile']);
});
