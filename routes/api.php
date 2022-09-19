<?php

use App\Http\Controllers\Auth\V1\AuthController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GeoLocation\CountryController;
use App\Http\Controllers\GeoLocation\StateController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Posts\TopicController;
use App\Http\Controllers\User\FollowController;
use App\Http\Controllers\User\UserController;
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

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::apiResource('posts', PostController::class);

Route::middleware('auth:api')->group(function () {
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('comment-reply', [CommentController::class, 'reply'])->name('comment.reply');
    Route::post('comment', [CommentController::class, 'comment'])->name('comment');
    Route::delete('comment/{comment}', [CommentController::class, 'delete'])->name('comment.delete');

    Route::get('get-my-rooms', [ChatController::class, 'getRooms'])->name('chat.get-my-rooms');
    Route::get('chat/message/{room_id}', [ChatController::class, 'getMessages'])->name('chat.getMessages');
    Route::get('chat/exist-room-or-create', [ChatController::class, 'getExistRoom'])->name('chat.getExistRoom');
    Route::get('chat/markAsRead', [ChatController::class, 'markAsRead'])->name('chat.markAsRead');
    Route::post('chat/send-message', [ChatController::class, 'sendMessage'])->name('chat.send-message');
    Route::get('follow/{user}', [FollowController::class, 'toggleFollow'])->name('user.toggleFollow');
    Route::apiResource('user', UserController::class)->names('user')->only(['update']);
});
Route::apiResource('topics', TopicController::class)->names('topics');
Route::apiResource('user', UserController::class)->names('user')->only(['show']);
Route::get('posts-by-user/{user}', [PostController::class, 'getPostsByUser'])->name('posts-by-user');
Route::post('upload-file', [Controller::class, 'httpResponse'])->name('upload-file');
Route::get('getComments', [CommentController::class, 'getComments'])->name('comment.get');
Route::apiResource('country', CountryController::class)->names('country')->only(['index', 'store', 'show']);
Route::apiResource('state', StateController::class)->names('state')->only(['index', 'store', 'show']);
Route::get('/auth/{driver}/{other}/{token}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProvider');
