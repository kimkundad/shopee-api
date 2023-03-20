<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OwnershopController;
use App\Http\Controllers\ShopController;

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