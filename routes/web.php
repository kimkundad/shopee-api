<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OwnershopController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::group(['middleware' => ['UserRole:superadmin|admin']], function() {

    Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index']);

    Route::resource('/admin/category', CategoryController::class);
    Route::post('/api/api_post_status_category', [App\Http\Controllers\CategoryController::class, 'api_post_status_category']);
    Route::get('api/del_cat/{id}', [App\Http\Controllers\CategoryController::class, 'del_cat']);

    Route::resource('/admin/ownershop', OwnershopController::class);
    Route::post('/api/api_post_status_ownershop', [App\Http\Controllers\OwnershopController::class, 'api_post_status_ownershop']);
    Route::get('api/del_ownershop/{id}', [App\Http\Controllers\OwnershopController::class, 'del_ownershop']);

    Route::resource('/admin/shops', ShopController::class);
    Route::post('/api/api_post_status_shops', [App\Http\Controllers\ShopController::class, 'api_post_status_shops']);
    Route::get('api/del_shops/{id}', [App\Http\Controllers\ShopController::class, 'del_shops']);

    Route::resource('/admin/products', ProductController::class);
    Route::post('/api/api_post_status_products', [App\Http\Controllers\ProductController::class, 'api_post_status_products']);
    Route::get('api/del_products/{id}', [App\Http\Controllers\ProductController::class, 'del_products']);

    Route::post('/api/upload_img_product/{id}', [App\Http\Controllers\ProductController::class, 'upload_img_product']);
    Route::get('/api/image_del/{id}', [App\Http\Controllers\ProductController::class, 'image_del']);
    Route::post('/admin/post_option1/{id}', [App\Http\Controllers\ProductController::class, 'post_option1']);
    Route::post('/admin/post_sup_option1/{id}', [App\Http\Controllers\ProductController::class, 'post_sup_option1']);

    Route::get('api/del_suboptions/{id}', [App\Http\Controllers\ProductController::class, 'del_suboptions']);
    Route::get('api/del_options/{id}', [App\Http\Controllers\ProductController::class, 'del_options']);

    Route::get('/admin/add_product_to_shop/{id}', [App\Http\Controllers\ShopController::class, 'add_product_to_shop']);
    Route::post('/api/api_add_product_shops', [App\Http\Controllers\ShopController::class, 'api_add_product_shops']);

});


Route::get('/images/{file}', function ($file) {
    $url = Storage::disk('do_spaces')->temporaryUrl(
      $file,
      now()->addMinutes(5)
    );
    if ($url) {
       return Redirect::to($url);
    }
    return abort(404);
})->where('file', '.+');