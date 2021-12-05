<?php

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


// We use this channel for every chat related events.
// Messages, Typing,
Broadcast::channel('chat.{sessionId}', function ($user, $sessionId) {
    // can add authentication check to use a chatroom
    return Auth::check();
});

Broadcast::channel('chat', function ($user) {
    // can add authentication check to use a chatroom
    if (Auth::check()) {
        return ['id' => $user->id, 'name' => $user->name];
    }
});