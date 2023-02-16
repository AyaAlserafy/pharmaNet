<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PushNotificationController;
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

Route::get('/w', function () {
    return view('welcome');
});
Route::get('/h', function () {
    return view('map');
});
Route::get('/', function () {
    return view('map2');
});

Route::post('send',[PushNotificationController::class, 'bulksend'])->name('bulksend');
    Route::get('all-notifications', [PushNotificationController::class, 'index']);
    Route::get('get-notification-form', [PushNotificationController::class, 'create']);
// Route::get('/send-web-push-notificaiton', [UserController::class, 'index'])->name('send-push.notificaiton');

// Route::post('/save-device-token', [UserController::class, 'saveDeviceToken'])->name('save-device.token');
// Route::post('/send-notification', [UserController::class, 'sendNotification'])->name('send.notification');

// //Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/home', function () {
//     return view('home');
// });
// Route::get('/', function () {
//     return view('welcome');
// });


// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::get('/app', [\App\http\Controllers\HomeController::class, 'index'])->name('home');
// Route::patch('/fcm-token', [\App\http\Controllers\HomeController::class, 'updateToken'])->name('fcmToken');
// Route::post('/send-notification', [\App\http\Controllers\HomeController::class, 'notification'])->name('notification');
// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/save-token', [App\Http\Controllers\HomeController::class, 'saveToken'])->name('save-token');
Route::post('/send-notification', [App\Http\Controllers\HomeController::class, 'sendNotification'])->name('send.notification');
