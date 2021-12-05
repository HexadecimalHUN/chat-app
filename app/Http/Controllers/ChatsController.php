<?php

namespace App\Http\Controllers;

use App\Events\MessageDelete;
use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ChatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show chats
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('chat');
    }
    
    
    // list every user except the logged in user!
    public function users()
    {
        return User::all()->except(Auth::id());
    }

    /**
     * Fetch all messages
     *
     * @return ChatMessage
     */
    public function fetchMessages(Request $request, $chat_room_id)
    {
        error_log('Messages fetched');

        return ChatMessage::with('rooms')
        ->  where(['chat_room_id' => $chat_room_id])
        ->  orderBy('created_at', 'ASC')
        // ->  limit(20)
        ->  get();
    }

    /**
     * Persist message to database
     *
     * @param  Request $request
     * @return Response
     */
    public function sendMessage(Request $request, $chat_room_id)
    {
        // construct message object and save it to the DB
        $new_message   =  ChatMessage::make();
        $new_message  ->  user_id = Auth::id();
        $new_message  ->  message = $request->message;
        $new_message  ->  chat_room_id = $chat_room_id;
        $new_message  ->  save();

      
        $request->chat_room_id = $chat_room_id;
        error_log($new_message);
        
        $user   =   User::find(Auth::id());
        $user   ->  last_seen = now();
        $user   ->  save();
        // Broadcast this message to the other user
        broadcast(new MessageSent($new_message))->toOthers();

        return $new_message;
    }


    public function fetchSession($friend_id)
    {
        error_log("this is the friend ID: ".$friend_id);
       
        $chat_room_id = ChatRoom::whereIn('user_id', [$friend_id,Auth::id()])
        ->select('chat_room_id', ChatRoom::raw('COUNT(chat_room_id) as count'))
        ->groupBy('chat_room_id')
        ->orderBy('count', 'DESC')
        ->get()
        ->first();
       
        if ($chat_room_id->count <2) {
            return ChatRoom::createRoomForChat($friend_id, Auth::id());
        } else {
            error_log($chat_room_id['chat_room_id']);
            return $chat_room_id['chat_room_id'];
        }
    }

    public function removeMessage($chat_room_id, $messageId)
    {
        error_log("messageID " . $messageId);

        $messageWannaBeRemoved  =   ChatMessage::where(
            [
            'id'                =>  $messageId,
            'chat_room_id'      =>  $chat_room_id,
            'user_id'           =>  Auth::id(),
            ]
        )->first();
        
        $messageWannaBeRemoved
        ->update(['is_removed' => true]);
        
        error_log($messageWannaBeRemoved);
        
        broadcast(new MessageDelete($messageWannaBeRemoved))->toOthers();

        return $messageWannaBeRemoved;
    }
}