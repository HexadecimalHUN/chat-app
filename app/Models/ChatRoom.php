<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatRoom extends Model
{
    use HasFactory;
    /**
     * A chat room can have many messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function user()
    {
        return $this->hasMany(User::class, "id", "user_id");
    }

    // TODO: Add session id here when we create the sessions for users
    public static function createRoomForChat($friend_id, $user_id)
    {
        $chatroom_id = uniqid();

        $friends_half_room = new ChatRoom;
        $friends_half_room->user_id = $friend_id;
        $friends_half_room->chat_room_id = $chatroom_id;
        $friends_half_room->save();
        
        $users_half_room = new ChatRoom;
        $users_half_room->user_id = $user_id;
        $users_half_room->chat_room_id = $chatroom_id;
        $users_half_room->save();

        return $chatroom_id;
    }
}