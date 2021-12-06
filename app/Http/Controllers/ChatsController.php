<?php

namespace App\Http\Controllers;

use App\Events\MessageDelete;
use App\Events\MessageSent;
use App\Events\BlockUnblockUser;
use App\Events\CheckMessages;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ChatRoom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        
        $user   =   User::find(Auth::id());
        $user   ->  last_seen = now();
        $user   ->  save();
        // Broadcast this message to the other user
        broadcast(new MessageSent($new_message))->toOthers();
        
        return $new_message;
    }
    /**
     * TODO:create function that counts the unseen messages;
     *
     */

    public function unseenMessageCount($chat_room_id, $user_id)
    {
        $unseen_messages_count = ChatMessage::where([
            'chat_room_id' => $chat_room_id,
            'user_id'           =>  Auth::id(),
            'seen_at'           =>  null,
        ])->count();

        error_log($unseen_messages_count);
    }

    /**
     * TODO:create function that SEE the unseen messages;
     *
     */

    public function checkMessages($chat_room_id, $friend_id)
    {
        $check_message_wanna_be = ChatMessage::where([
            'chat_room_id'      =>  $chat_room_id,
            'user_id'           =>  $friend_id,
            'seen_at'           =>  null,
        ])->get();

        if (!(int) empty($check_message_wanna_be[0])) {
            ChatMessage::where([
                'chat_room_id'      =>  $chat_room_id,
                'user_id'           =>  $friend_id,
                'seen_at'           =>  null,
            ])->update(
                ['seen_at'           =>  now()]
            );
            
            broadcast(new CheckMessages($chat_room_id))->toOthers();

            return "Messages seen by the user!";
        }

        return "No message to check!";
    }
    
    /**
     * This is for figuring out what chatroom connected to users
     * Need to be improved so more than one user chat room can be created
     */
    public function fetchChatRoomId($friend_id)
    {
        $chat_room_id = ChatRoom::whereIn('user_id', [$friend_id,Auth::id()])
        ->select('chat_room_id', ChatRoom::raw('COUNT(chat_room_id) as count'))
        ->groupBy('chat_room_id')
        ->orderBy('count', 'DESC')
        ->get()
        ->first();
       
        if ($chat_room_id->count <2) {
            return ChatRoom::createRoomForChat($friend_id, Auth::id());
        } else {
            return $chat_room_id['chat_room_id'];
        }
    }

    public function fetchChatRoom($chat_room_id)
    {
        return ChatRoom::where([
            'chat_room_id' => $chat_room_id,
            'user_id' => Auth::id()
        ])->get()->first();
    }


    /**
     * This is for removing a message
     */
    public function removeMessage($chat_room_id, $messageId)
    {
        $messageWannaBeRemoved  =   ChatMessage::where(
            [
            'id'                =>  $messageId,
            'chat_room_id'      =>  $chat_room_id,
            'user_id'           =>  Auth::id(),
            ]
        )->first();
            
        $messageWannaBeRemoved
            ->update(['is_removed' => true]);
            
        broadcast(new MessageDelete($messageWannaBeRemoved))->toOthers();
        
        return $messageWannaBeRemoved;
    }
    

    /**
     * This is for blocking and unblocking a user
     */
    public function blockChatWithUser($chat_room_id)
    {
        $chatToBlock = ChatRoom::where([
            'chat_room_id' => $chat_room_id
        ]);
        $chatToBlock ->update([
                'is_blocked'      => true,
                'blocked_by'    => Auth::id(),
            ]);
        broadcast(new BlockUnblockUser($chat_room_id, true, Auth::id()))->toOthers();
    }
    
    public function unblockChatWithUser($chat_room_id)
    {
        $chatToBlock = ChatRoom::where([
            'chat_room_id' => $chat_room_id
        ]);
        $chatToBlock->update([
                'is_blocked'      => false,
                'blocked_by'    => null
            ]);
        
        broadcast(new BlockUnblockUser($chat_room_id, false, Auth::id()))->toOthers();
    }
}