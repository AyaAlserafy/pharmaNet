<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use thiagoalessio\TesseractOCR\TesseractOCR;

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

Route::post('add_user', [\App\http\Controllers\UserController::class, 'register']);
Route::post('login', [\App\http\Controllers\Controller::class, 'loginn']);

Route::post('forget_password', [\App\http\Controllers\UserController::class, 'forget_password']);
Route::post('code', [\App\http\Controllers\UserController::class, 'code']);
Route::post('change_password', [\App\http\Controllers\UserController::class, 'change_password']);
Route::post('notifications', [\App\http\Controllers\Controller::class, 'n']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('logout', [\App\http\Controllers\Controller::class, 'logout']);

    Route::post('add_location', [\App\http\Controllers\UserController::class, 'add_location']);
    Route::get('distance', [\App\http\Controllers\UserController::class, 'distance']);
    Route::get('nearest_pharmacies', [\App\http\Controllers\UserController::class, 'nearest_pharmacies']);
    Route::post('search_medicine', [\App\http\Controllers\UserController::class, 'search_medicine']);
    Route::post('addTocart', [\App\http\Controllers\UserController::class, 'addTocart']);
    Route::post('add_prescription', [\App\http\Controllers\UserController::class, 'add_prescription']);
    Route::post('cansel_order', [\App\http\Controllers\UserController::class, 'cansel_order']);
    Route::post('get_byCategoury', [\App\http\Controllers\UserController::class, 'get_byCategoury']);
    Route::get('get_company', [\App\http\Controllers\UserController::class, 'get_company']);
    Route::get('get_categoury', [\App\http\Controllers\UserController::class, 'get_categoury']);
    Route::get('get_class', [\App\http\Controllers\UserController::class, 'get_class']);



    Route::post('most_requested', [\App\http\Controllers\pharmacyController::class, 'most_requested']);
    Route::post('add_product', [\App\http\Controllers\pharmacyController::class, 'add_product']);
    Route::post('accept_order', [\App\http\Controllers\pharmacyController::class, 'accept_order']);
    Route::post('delete_product', [\App\http\Controllers\PharmacyController::class, 'delete_product']);
    Route::post('sale', [\App\http\Controllers\pharmacyController::class, 'sale']);
    Route::post('read_prescription', [\App\http\Controllers\pharmacyController::class, 'read_prescription']);
    Route::post('activep', [\App\http\Controllers\pharmacyController::class, 'active']);
    Route::get('get_all_ordersP', [\App\http\Controllers\pharmacyController::class, 'get_all_orders']);
    Route::post('add_allternative', [\App\http\Controllers\pharmacyController::class, 'add_allternative']);
    Route::post('delete_order', [\App\http\Controllers\pharmacyController::class, 'delete_order']);




    Route::post('actived', [\App\http\Controllers\DriverController::class, 'active']);
    Route::get('getAllOrderV', [\App\http\Controllers\DriverController::class, 'getAllOrder']);
    Route::post('accept_order_driver', [\App\http\Controllers\DriverController::class, 'accept_order']);
    Route::post('order_complate', [\App\http\Controllers\DriverController::class, 'order_complate']);
    Route::post('delete_order_driver', [\App\http\Controllers\DriverController::class, 'delete_order_driver']);
});

Route::post('product_detalies', [\App\http\Controllers\UserController::class, 'get_product_detalies']);
Route::get('all_pharmacy', [\App\http\Controllers\UserController::class, 'all_pharmacy']);
Route::post('pharma_med', [\App\http\Controllers\UserController::class, 'pharma_med']);


Route::post('add_pharmacy', [\App\http\Controllers\AdminController::class, 'add_pharmacy']);
Route::post('add_driver', [\App\http\Controllers\AdminController::class, 'add_driver']);


Route::post('add', [\App\http\Controllers\UserController::class, 'add']);
