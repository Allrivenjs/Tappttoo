<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

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

Route::get('/{type}/{path}', [Controller::class, 'getImages'])
    ->name('getAnyImage')->whereIn('type', ['public', 'private']);


Route::get('/auth/{driver}/redirect', [Controller::class,'redirectToProvider'])
    ->name('social.auth');

Route::get('/auth/{driver}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProvider');
