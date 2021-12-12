<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', [App\Http\Controllers\ChatsController::class, 'index'])->name('chat');

Route::get('/chat', [App\Http\Controllers\ChatsController::class, 'index']);

Route::get('/chat/users', [App\Http\Controllers\ChatsController::class, 'users']);



Route::put('/chat/user/update{friendId}', [App\Http\Controllers\ChatsController::class, 'userLastSeen']);

// By friend ID find the room
// for more user in one chat this need to be modified
Route::get('/chat/room/{friendId}', [App\Http\Controllers\ChatsController::class, 'fetchChatRoomId']);
Route::get('/chat/room/data/{chatroomId}', [App\Http\Controllers\ChatsController::class, 'fetchChatRoom']);

/**
 *  Message related endpoints
 */
Route::get('/chat/unseen-messages', [App\Http\Controllers\ChatsController::class, 'unseenMessageCount']);

Route::put('/chat/{chatroomId}/check-messages/{userId}', [App\Http\Controllers\ChatsController::class, 'checkMessages']);

Route::put('/chat/{chatroomId}/remove-message/{messageId}', [App\Http\Controllers\ChatsController::class, 'removeMessage']);

Route::get('/chat/{chatroomId}/messages', [App\Http\Controllers\ChatsController::class, 'fetchMessages']);
Route::post('/chat/{chatroomId}/messages', [App\Http\Controllers\ChatsController::class, 'sendMessage']);


/**
 *  Pin, change and delete pinned message
 */
Route::post('/chat/{chatroomId}/pin-message', [App\Http\Controllers\ChatsController::class, 'pinMessage']);
Route::get('/chat/{chatroomId}/get-pinned', [App\Http\Controllers\ChatsController::class, 'getPinnedMessage']);
Route::delete('/chat/{chatroomId}/delete-pinned', [App\Http\Controllers\ChatsController::class, 'deletePinnedMessage']);


Route::put('/chat/{chatroomId}/block', [App\Http\Controllers\ChatsController::class, 'blockChatWithUser']);
Route::put('/chat/{chatroomId}/unblock', [App\Http\Controllers\ChatsController::class, 'unblockChatWithUser']);