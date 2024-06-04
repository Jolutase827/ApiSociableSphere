<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\PayPalController;
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

Route::apiResource('users', UsersController::class)->only(['update','destroy','show']);

Route::get('new_users/{id}',[UsersController::class, 'getUsersLimit20']);
Route::post('find_users_like/{id}',[UsersController::class, 'getUsersLimit20ByName']);

Route::apiResource('posts', PostController::class);

Route::apiResource('messages', MessageController::class);

Route::apiResource('chats', ChatController::class)->only('index','show');

Route::apiResource('comments', CommentController::class)->only(['store','update','destroy']);


Route::post('like', [PostController::class, 'addLike']);
Route::delete('like/{id}', [PostController::class, 'deleteLike']);

Route::post('new_profile_image', [UsersController::class, 'updateImage']);

Route::post('follow', [UsersController::class, 'followUser']);
Route::delete('follow/{id}', [UsersController::class, 'unfollowUser']);

Route::get('followed/{id}', [FollowerController::class, 'followedUsers']);
Route::get('follows/{id}', [FollowerController::class, 'followsUser']);

Route::post('login', [LoginController::class, 'login']);
Route::post('register', [LoginController::class, 'register']);

Route::get("posts_with_algorithm",[PostController::class,'getPostWithAlgoritm']);
Route::get("adds",[PostController::class,'getAdds']);
Route::get("payment/{post_id}",[PostController::class,'createPayment']);
Route::get("claim/{post_id}",[PostController::class,'createClaim']);


Route::post("chat",[ChatController::class,'showOrCreateChat']);

Route::get("wallet/{amount}",[UsersController::class, 'wallet']);

Route::post('/withdraw', [PayPalController::class, 'withdraw']);
