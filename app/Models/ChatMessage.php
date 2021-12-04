<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    public function user()
    {
        // The class that embodies the table in the database
        // The column name in that table that serves as the ID
        // And the column name here that is the store the foreign key
        return $this->belongsTo(User::class, 'id', 'user_id');
    }

    public function rooms()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id', 'chat_room_id');
    }
}