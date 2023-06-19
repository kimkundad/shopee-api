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
    Route::post('/hookSellPang', [App\Http\Controllers\ApiController::class, 'hookSellPang']);
    Route::get('/get_category_all', [App\Http\Controllers\ApiController::class, 'get_category_all']);
    Route::get('/allProduct/{id}', [App\Http\Controllers\ApiController::class, 'get_all_product']);
    Route::post('/getProduct', [App\Http\Controllers\ApiController::class, 'get_product']);
    Route::put('/setActiveProduct', [App\Http\Controllers\ApiController::class, 'set_active_product']);
    Route::put('/setActiveAllProduct', [App\Http\Controllers\ApiController::class, 'set_active_AllProduct']);
    Route::post('/deleteProduct/{id}', [App\Http\Controllers\ApiController::class, 'delete_product']);
    Route::post('/addProduct', [App\Http\Controllers\ApiController::class, 'addProduct']);
    Route::get('/shop/{id}', [App\Http\Controllers\ApiController::class, 'get_shop_name']);
    Route::post('/searchProduct/{id}', [App\Http\Controllers\ApiController::class, 'search_product']);
    Route::get('/getAllProduct', [App\Http\Controllers\ApiController::class, 'get_allproduct']);
    Route::get('/getAllShops', [App\Http\Controllers\ApiController::class, 'get_all_shops']); // route ดึงข้อมูลร้านค้าออกมาทั้งหมด create by อั้นเอง
    Route::get('/getSearchShops', [App\Http\Controllers\ApiController::class, 'get_search_shops']); // route ค้นหาชื่อร้านค้า create by อั้นเอง
    Route::get('/getSearchProduct', [App\Http\Controllers\ApiController::class, 'getSearchProduct']); // route ค้นหาชื่อร้านค้า create by อั้นเอง
    Route::get('/getSearchDateShops', [App\Http\Controllers\ApiController::class, 'get_search_date_shops']); // route ค้นหาวันที่สร้างร้านค้า create by อั้นเอง
    Route::get('/getFilterShops', [App\Http\Controllers\ApiController::class, 'get_filter_shops']); // route ในการ filter ร้า่นค้า ออกมาตาม ตัวอักษร, วันที่สร้าง, วันที่อัพเดท create by อั้นเอง
    Route::post('/addProductToCart', [App\Http\Controllers\ApiController::class, 'addProductToCart']);
    Route::post('/getAllCartItem', [App\Http\Controllers\ApiController::class, 'getAllCartItem']);
    Route::get('/getAllUsers', [App\Http\Controllers\ApiController::class, 'getAllUsers']); // route ของการดึงข้อมูล users ออกมาทั้งหมด create by อั้นเอง
    Route::post('/createSubAdmin', [App\Http\Controllers\ApiController::class, 'createSubAdmin']); // route สร้างข้อมูล sub-admin ขึ้นมา create by อั้นเอง
    Route::post('/updateSubAdmin', [App\Http\Controllers\ApiController::class, 'updateSubAdmin']); // route อัพเดทข้อมูล sub-admin create by อั้นเอง
    Route::post('/deleteSubAdmin', [App\Http\Controllers\ApiController::class, 'deleteSubAdmin']); // route ลบข้อมูลข้อง sub-admin ออก create by อั้นเอง
    Route::get('/getSearchDateSubAdmin', [App\Http\Controllers\ApiController::class, 'getSearchDateSubAdmin']); // route การทำ filter ค้นหาข้อมูล sub-admin จาก วันที่ที่สร้าง create by อั้นเอง
    Route::get('/getSearchName', [App\Http\Controllers\ApiController::class, 'getSearchName']); // route การทำ filter ค้นหาข้อมูล sub-admin จากชื่อ create by อั้นเอง
    Route::post('/deleteCartItem', [App\Http\Controllers\ApiController::class, 'deleteItemCart']);
    Route::post('/createShop', [App\Http\Controllers\ApiController::class, 'createShop']); // route สร้างข้อมูลร้านค้า create by อั้นเอง
    Route::post('/editShop', [App\Http\Controllers\ApiController::class, 'editShop']); // route แก้ไขข้อมูลร้านค้า create by อั้นเอง
    Route::post('/DeleteShop', [App\Http\Controllers\ApiController::class, 'DeleteShop']); // route ลบข้อมูลร้านค้า create by อั้นเอง
    Route::post('/changeStatusShop', [App\Http\Controllers\ApiController::class, 'changeStatusShop']); // route เปลี่ยนสถานะ เปิด/ปิด ของร้านค้า create by อั้นเอง
    Route::get('/getListProduct/{shopid}', [App\Http\Controllers\ApiController::class, 'getListProduct']); // route ดึง product ของแต่ละร้านค้า by อั้นเอง
    Route::get('/getCategoryShop/{shopid}', [App\Http\Controllers\ApiController::class, 'getCategoryShop']); // route ดึง หมวดหมู่ ร้านค้านั้นๆ by อั้นเอง
    Route::post('/addCategory', [App\Http\Controllers\ApiController::class, 'addCategory']); // route สร้างหมวดหมู่ หมวดหมู่ by อั้นเอง
    Route::post('/EditCategory', [App\Http\Controllers\ApiController::class, 'EditCategory']); // route แก้ไขหมวดหมู่ หมวดหมู่ by อั้นเอง
    Route::post('/editProduct/{id}', [App\Http\Controllers\ApiController::class, 'editProduct']); // route แก้ไขสินค้า by อั้นเอง
    Route::post('/deleteImgProduct/{id}', [App\Http\Controllers\ApiController::class, 'deleteImgProduct']); // route ลบรูปสินค้า by อั้นเอง
    Route::post('/deleteImgSubProduct/{id}', [App\Http\Controllers\ApiController::class, 'deleteImgSubProduct']); // route ลบรูปรองสินค้า by อั้นเอง
    Route::post('/deleteCategory/{id}', [App\Http\Controllers\ApiController::class, 'deleteCategory']); // route ลบหมวดหมู่ by อั้นเอง
    Route::post('/deleteOptionProduct/{id}', [App\Http\Controllers\ApiController::class, 'deleteOptionProduct']); // route ลบหมวดหมู่ by อั้นเอง
    Route::post('/deleteSubOptionProduct/{id}', [App\Http\Controllers\ApiController::class, 'deleteSubOptionProduct']); // route ลบหมวดหมู่ by อั้นเอง
    Route::post('/deleteTitleSubOptionProduct/{productId}', [App\Http\Controllers\ApiController::class, 'deleteTitleSubOptionProduct']); // route ลบหมวดหมู่ by อั้นเอง
    Route::post('/deleteTitleOptionProduct/{productId}', [App\Http\Controllers\ApiController::class, 'deleteTitleOptionProduct']); // route ลบหมวดหมู่ by อั้นเอง
    Route::post('/editOptionProduct', [App\Http\Controllers\ApiController::class, 'editOptionProduct']); // route ลบหมวดหมู่ by อั้นเอง
    Route::post('/createdOrder', [App\Http\Controllers\ApiController::class, 'created_order']);
    Route::post('/getAllOrder', [App\Http\Controllers\ApiController::class, 'getAllOrder']);
    Route::post('/getOrder', [App\Http\Controllers\ApiController::class, 'getOrder']);
    Route::post('/editUser', [App\Http\Controllers\ApiController::class, 'editUser']);
    Route::post('/editAvatar', [App\Http\Controllers\ApiController::class, 'editAvatar']);
    Route::post('/getUser', [App\Http\Controllers\ApiController::class, 'getUser']);
    Route::post('/newAddress', [App\Http\Controllers\ApiController::class, 'newAddress']);
    Route::post('/getAllAddress', [App\Http\Controllers\ApiController::class, 'getAllAddress']);
    Route::post('/deleteAddress', [App\Http\Controllers\ApiController::class, 'deleteAddress']);
    Route::post('/getAddress', [App\Http\Controllers\ApiController::class, 'getAddress']);
    Route::post('/setDefaultAddress', [App\Http\Controllers\ApiController::class, 'setDefaultAddress']);
    Route::post('/editAddress', [App\Http\Controllers\ApiController::class, 'editAddress']);
    Route::post('/addOptionProduct', [App\Http\Controllers\ApiController::class, 'addOptionProduct']);
    Route::post('/getOrders', [App\Http\Controllers\ApiController::class, 'getOrders']);
    Route::post('/setStatusOrders', [App\Http\Controllers\ApiController::class, 'setStatusOrders']);
    Route::post('/setStatusOrdersMulti', [App\Http\Controllers\ApiController::class, 'setStatusOrdersMulti']);
    Route::post('/addTrackingOrder', [App\Http\Controllers\ApiController::class, 'addTrackingOrder']);
    Route::post('/addTrackingOrderKerry', [App\Http\Controllers\ApiController::class, 'addTrackingOrderKerry']);
    Route::post('/addTrackingOrderFlash', [App\Http\Controllers\ApiController::class, 'addTrackingOrderFlash']);
    Route::post('/deleteSubOption', [App\Http\Controllers\ApiController::class, 'deleteSubOption']);
    Route::post('/deleteOption', [App\Http\Controllers\ApiController::class, 'deleteOption']);

    Route::post('/getMessage', [App\Http\Controllers\ApiController::class, 'getMessage']);
    Route::post('/sendMessage', [App\Http\Controllers\ApiController::class, 'sendMessage']);
    Route::post('/getBank', [App\Http\Controllers\ApiController::class, 'getBankaccount']);

    Route::get('/getCategory/{id}' , [App\Http\Controllers\ApiController::class, 'getCategory']);

    Route::post('/getUserChats' , [App\Http\Controllers\ApiController::class, 'getUserChats']);
    Route::post('/searchUserChats' , [App\Http\Controllers\ApiController::class, 'search_users_chats']);

    Route::post('/getReports' , [App\Http\Controllers\ApiController::class, 'getReports']);
    Route::post('/totalOrders' , [App\Http\Controllers\ApiController::class, 'total_orders']);
    Route::post('/getDetailCutomer' , [App\Http\Controllers\ApiController::class, 'detail_customer']);

    Route::post('/countOrder' , [App\Http\Controllers\ApiController::class, 'count_orders']);

    Route::post('/dashboard' , [App\Http\Controllers\ApiController::class, 'dashboard']);

    Route::post('/confirmPayment' , [App\Http\Controllers\ApiController::class, 'confirm_payment']);


    Route::post('/getOwnershops' , [App\Http\Controllers\ApiController::class, 'getOwnershops']);

    Route::post('/updateOwnerShop' , [App\Http\Controllers\ApiController::class, 'updateOwnerShop']);

    Route::get('/allBanks' , [App\Http\Controllers\ApiController::class, 'getAllBanks']);

    Route::post('/addBankAccount' , [App\Http\Controllers\ApiController::class, 'addBankAccount']);

    Route::post('/activeBankAcc' , [App\Http\Controllers\ApiController::class, 'setActiveBankacc']);

    Route::post('/activeCOD' , [App\Http\Controllers\ApiController::class, 'getActiveCOD']);

    Route::post('/delBankAcc' , [App\Http\Controllers\ApiController::class, 'deleteBankaccount']);

    Route::post('/updateBankaccount' , [App\Http\Controllers\ApiController::class, 'updateBankaccount']);

    Route::get('/getOwnerSetting' , [App\Http\Controllers\ApiController::class, 'getOwnerSetting']);
    Route::post('/setNotification' , [App\Http\Controllers\ApiController::class, 'settingNoti']);
    Route::get('/newNoti/{id}', [App\Http\Controllers\ApiController::class, 'newNoti']);
    Route::get('/getNoti/{id}', [App\Http\Controllers\ApiController::class, 'getNoti']);
    Route::post('/readNoti', [App\Http\Controllers\ApiController::class, 'readNoti']);

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
