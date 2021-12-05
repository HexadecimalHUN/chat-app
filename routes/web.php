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

// By friend ID find the session
// for more user in one chat this need to be modified
Route::get('/chat/session/{friendId}', [App\Http\Controllers\ChatsController::class, 'fetchSession']);



Route::get('/chat/{sessionId}/messages', [App\Http\Controllers\ChatsController::class, 'fetchMessages']);
Route::post('/chat/{sessionId}/messages', [App\Http\Controllers\ChatsController::class, 'sendMessage']);


Route::put('/chat/{sessionId}/remove-message/{messageId}', [App\Http\Controllers\ChatsController::class, 'removeMessage']);
