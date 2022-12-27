<?php

use App\Events\MessageNotification;
use App\Http\Controllers\Auth\V1\AuthController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FinderController;
use App\Http\Controllers\GeoLocation\CountryController;
use App\Http\Controllers\GeoLocation\StateController;
use App\Http\Controllers\Posts\LikeablePostController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Posts\TopicController;
use App\Http\Controllers\User\FollowController;
use App\Http\Controllers\User\TattooArtistController;
use App\Http\Controllers\User\UserController;
use App\Notifications\AnymoreNotification;
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

Route::get('finder', [FinderController::class, 'index'])
    ->name('finder');

Route::get('show-posts-by-type', [FinderController::class, 'showPostsByType'])
    ->name('show-posts-by-type');

Route::apiResource('posts', PostController::class)->except(['update']);
Route::post('posts/{post}/update', [PostController::class, 'update'])->name('posts.update');

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

    Route::put('user', [UserController::class, 'update'])->name('userUpdate');

    Route::post('user/change-avatar', [UserController::class, 'updateAvatar'])->name('user.updateAvatar');

    Route::get('user/me', [UserController::class, 'mePosts'])->name('user.me');

    Route::post('user/preferences', [UserController::class, 'assignPreferences'])->name('user.preferences');

    Route::post('post/{post}/like', [LikeablePostController::class, 'like'])->name('post.like');
    Route::post('post/{post}/unlike', [LikeablePostController::class, 'unlike'])->name('post.unlike');
    Route::get('post/{post}/like-count', [LikeablePostController::class, 'countLikes'])->name('post.likeCount');

    Route::group(['prefix' => 'tattoo-artist'], function () {
        Route::put('price', [TattooArtistController::class, 'updatePrice'])->name('companyUpdatePrice');
        Route::put('status', [TattooArtistController::class, 'updateStatus'])->name('companyUpdateStatus');
        Route::put('instagram', [TattooArtistController::class, 'updateInstagram'])->name('companyUpdateInstagram');
        Route::put('name-company', [TattooArtistController::class, 'updateNameCompany'])->name('companyUpdateNameCompany');
    });
});

Route::get('followings/{user}', [FollowController::class, 'followings'])->name('user.followings');
Route::get('followers/{user}', [FollowController::class, 'followers'])->name('user.followers');


Route::apiResource('topics', TopicController::class)->names('topics');
Route::apiResource('user', UserController::class)->names('user')->only(['show']);
Route::get('posts-by-user/{user}', [PostController::class, 'getPostsByUser'])->name('posts-by-user');
Route::post('upload-file', [Controller::class, 'httpResponse'])->name('upload-file');
Route::get('getComments', [CommentController::class, 'getComments'])->name('comment.get');

Route::apiResource('country', CountryController::class)->names('country')->only(['index', 'store', 'show']);
Route::apiResource('state', StateController::class)->names('state')->only(['index', 'store', 'show']);

Route::get('/auth/{driver}/{other}/{token}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProvider');

Route::post('send-event', function (){
    \Illuminate\Support\Facades\Notification::send(\App\Models\User::query()->first(), new AnymoreNotification('This is our first broadcast message'));
});
