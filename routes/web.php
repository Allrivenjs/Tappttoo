<?php

use App\Http\Controllers\Controller;
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


Route::get('/a', function () {
    return view('welcome');
});

Route::get('/', [Controller::class, 'getImages'])
    ->name('getAnyImage');


Route::get('/auth/{driver}/redirect', [Controller::class,'redirectToProvider'])
    ->name('social.auth');

Route::get('/auth/{driver}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProvider');

