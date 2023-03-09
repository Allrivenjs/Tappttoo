<?php

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

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


Route::get('/', function () {
    return view('welcome');
});

Route::get('/image', [Controller::class, 'getImages'])
    ->name('getAnyImage');


Route::get('/auth/{driver}/redirect', [Controller::class,'redirectToProvider'])
    ->name('social.auth');

Route::get('/auth/{driver}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProviderWeb');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::get('policy', function () {
    return view('politicas');
})->name('policy');

Route::get('terms', function () {
    return view('terminos');
})->name('terms');


Route::get('phpinfo', function () {
    phpinfo();
});

Route::get('payment', [PaymentController::class, 'webhook'])->name('payment');
Route::get('payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

