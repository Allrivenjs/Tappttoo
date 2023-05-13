<?php

use App\Events\MessageNotification;
use App\Http\Controllers\Auth\V1\AuthController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FinderController;
use App\Http\Controllers\GeoLocation\CountryController;
use App\Http\Controllers\GeoLocation\StateController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Posts\LikeablePostController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Posts\TopicController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\Report\ReportProblem;
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

    Route::get('check-auth', [AuthController::class, 'checkAuth'])->name('checkAuth');

    Route::post('create-payment', [PaymentController::class, 'createPayment'])->name('createPayment');
    Route::get('plans', [PaymentController::class, 'getPlans'])->name('getPlans');

    Route::post('comment-reply', [CommentController::class, 'reply'])->name('comment.reply');
    Route::post('comment', [CommentController::class, 'comment'])->name('comment');
    Route::delete('comment/{comment}', [CommentController::class, 'delete'])->name('comment.delete');

    Route::get('chat/markAsRead', [ChatController::class, 'markAsRead'])->name('chat.markAsRead');
    Route::get('chat/exist-room-or-create', [ChatController::class, 'getExistRoom'])->name('chat.getExistRoom');
    Route::get('get-my-rooms', [ChatController::class, 'getRooms'])->name('chat.get-my-rooms');
    Route::get('chat/message/{room_id}', [ChatController::class, 'getMessages'])->name('chat.getMessages');
    Route::post('chat/send-message', [ChatController::class, 'sendMessage'])
        ->withoutMiddleware('throttle:api')
        ->middleware('throttle:300,1')
        ->name('chat.send-message');

    Route::apiResource('quotation', QuotationController::class)->only('store', 'show')->names('quotation');

    Route::get('follow/{user}', [FollowController::class, 'toggleFollow'])->name('user.toggleFollow');

    Route::put('user', [UserController::class, 'update'])->name('userUpdate');

    Route::post('user/change-avatar', [UserController::class, 'updateAvatar'])->name('user.updateAvatar');

    Route::get('user/me', [UserController::class, 'mePosts'])->name('user.me');

    Route::post('user/preferences', [UserController::class, 'assignPreferences'])->name('user.preferences');

    Route::post('post/{post}/like', [LikeablePostController::class, 'like'])->name('post.like');
    Route::post('post/{post}/unlike', [LikeablePostController::class, 'unlike'])->name('post.unlike');
    Route::get('post/{post}/like-count', [LikeablePostController::class, 'countLikes'])->name('post.likeCount');

    Route::get('post/like-by-me', [LikeablePostController::class,'getMyLovelyPosts'])->name('post.likeByMe');

    Route::post('update-password', [AuthController::class, 'updatePassword'])->name('user.updatePassword');

    Route::post('delete-account', [UserController::class, 'deleteMyAccount'])->name('user.deleteAccount');

    Route::post('report', [ReportProblem::class, 'reportProblem'])->name('user.report');
    Route::get('mark-resolved/{report}', [ReportProblem::class, 'markAsResolved'])->name('user.markAsResolved');
    Route::get('get-reports', [ReportProblem::class, 'getReportedProblems'])->name('user.getReportedProblems');

    Route::get('hidden-posts/{post}', [PostController::class, 'hiddenPost'])->name('post.hiddenPosts');
    Route::get('unhidden-posts/{post}', [PostController::class, 'unhiddenPost'])->name('post.unhiddenPosts');


    Route::middleware('can:ADMIN')->group(function () {
        Route::post('user/delete/{user}', [UserController::class, 'delete'])->name('user.delete');
        Route::post('post/delete/{post}', [PostController::class, 'delete'])->name('post.delete');

        Route::post('create-plan', [PaymentController::class, 'storePlan'])->name('storePlan');
        Route::post('update-plan/{plan}', [PaymentController::class, 'updatePlan'])->name('updatePlan');

    });


    Route::group(['prefix' => 'tattoo-artist'], function () {
        Route::put('price', [TattooArtistController::class, 'updatePrice'])->name('companyUpdatePrice');
        Route::put('status', [TattooArtistController::class, 'updateStatus'])->name('companyUpdateStatus');
        Route::put('instagram', [TattooArtistController::class, 'updateInstagram'])->name('companyUpdateInstagram');
        Route::put('name-company', [TattooArtistController::class, 'updateNameCompany'])->name('companyUpdateNameCompany');
        Route::post('assign-image', [TattooArtistController::class, 'assignImages'])->name('assign-image');
    });
});

Route::get('followings/{user}', [FollowController::class, 'followings'])->name('user.followings');
Route::get('followers/{user}', [FollowController::class, 'followers'])->name('user.followers');
Route::get('random-users', [FollowController::class, 'randomArtist'])->name('user.randomArtist');

Route::apiResource('topics', TopicController::class)->names('topics');
Route::apiResource('user', UserController::class)->names('user')->only(['show']);
Route::get('posts-by-user/{user}', [PostController::class, 'getPostsByUser'])->name('posts-by-user');
Route::post('upload-file', [Controller::class, 'httpResponse'])->name('upload-file');
Route::get('getComments', [CommentController::class, 'getComments'])->name('comment.get');

Route::apiResource('country', CountryController::class)->names('country')->only(['index', 'store', 'show']);
Route::apiResource('state', StateController::class)->names('state')->only(['index', 'store', 'show']);

Route::get('/auth/{driver}/{other}/{token}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProvider');
Route::get('/auth/{driver}/callback', [Controller::class,'redirectToCallbackSocialProvider'])
    ->name('redirectToCallbackSocialProviderApi');

Route::post('send-event', function (){
    \Illuminate\Support\Facades\Notification::send(\App\Models\User::query()->first(), new AnymoreNotification('This is our first broadcast message'));
});

Route::post('send-email-to-reset-password', [AuthController::class, 'sendEmailToResetPassword'])
    ->middleware('guest')
    ->name('send-email-to-reset-password');

Route::post('reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('guest')->name('reset-password');

Route::post('delete-backend-for-not-pay', [Controller::class, 'deleteBackendForNotPay'])->name('delete-backend-for-not-pay');

Route::post('payment-confirm', [PaymentController::class, 'paymentConfirm'])->name('payment.confirm');
