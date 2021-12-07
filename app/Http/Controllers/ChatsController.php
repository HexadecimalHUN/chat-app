<?php

namespace App\Http\Controllers;

use App\Events\MessageDelete;
use App\Events\MessageSent;
use App\Events\BlockUnblockUser;
use App\Events\CheckMessages;
use App\Events\PinMessage;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ChatRoom;
use App\Models\PinnedMessages;
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
     * @return ChatMessage[]
     * @method GET
     * @param $chat_room_id
     */
    public function fetchMessages($chat_room_id)
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
     * @method POST
     */
    public function sendMessage(Request $request, $chat_room_id)
    {
        // construct message object and save it to the DB
        $new_message   =  ChatMessage::make();
        $new_message  ->  user_id = Auth::id();
        $new_message  ->  message = $request->message;
        $new_message  ->  chat_room_id = $chat_room_id;
        $new_message  ->  save();

        // $request->chat_room_id = $chat_room_id;
        
        
        // Broadcast this message to the other user
        broadcast(new MessageSent($new_message))->toOthers();
        
        return $new_message;
    }
    
    
    public function userLastSeen($friend_id)
    {
        $user   =   User::find($friend_id);
        $user   ->  last_seen = now();
        $user   ->  save();

        return $user->last_seen;
    }


    /**
     * @method GET
     * @return array
     */

    public function unseenMessageCount()
    {
        $rooms_with_loged_in_user = ChatRoom::where([
            "user_id"  => Auth::id()
        ])->get();

        $unseen_messages_array = [];

        foreach ($rooms_with_loged_in_user as &$chat_room) {
            $unseen_messages_count = ChatMessage::select([
                'chat_room_id',
                ChatRoom::raw('user_id as sent_to'),
                ChatRoom::raw('COUNT(chat_room_id) as count'),
                ])
            ->where([
                'chat_room_id'      => $chat_room->chat_room_id,
                'seen_at'           =>  null,
            ])
            ->where('user_id', '!=', Auth::id())
            ->groupBy('chat_room_id', 'user_id')
            ->first();
            
            if (!empty($unseen_messages_count)) {
                array_push($unseen_messages_array, $unseen_messages_count);
            }
        }
        
        return $unseen_messages_array;
    }

    /**
     * If the user open the chat window or recieve a new message
     *  this function runs
     *
     */

    public function checkMessages($chat_room_id, $friend_id)
    {
        $check_message_wanna_be = ChatMessage::where([
            'chat_room_id'      =>  $chat_room_id,
            'user_id'           =>  $friend_id,
            'seen_at'           =>  null,
        ]);

        if (!(int) empty($check_message_wanna_be->get()[0])) {
            $check_message_wanna_be->update(
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
    
    
    /**
     * Pin message function for handling pin delete and change pinned message
     */
    public function pinMessage(Request $request)
    {
        $pin_already_exists = PinnedMessages::where(["chat_room_id" => $request->chat_room_id])->first();
        
        if ((int) empty($pin_already_exists)) {
            $new_pinned_message   =  PinnedMessages::make();
            $new_pinned_message   -> chat_room_id = $request->chat_room_id;
            $new_pinned_message   -> pinned_by = $request->user_id;
            $new_pinned_message   -> message = $request->message;
            $new_pinned_message   -> save();
        
        // broadcast(new PinMessage($request->chat_room_id))->toOthers();
        } else {
            $pin_already_exists -> message = $request->message;
            $pin_already_exists -> pinned_by = $request->user_id;
            $pin_already_exists -> save();

            // broadcast(new PinMessage($request->chat_room_id))->toOthers();
        }
        
        broadcast(new PinMessage($request->chat_room_id))->toOthers();
        return "Message Pinned!";
    }

    public function getPinnedMessage($chat_room_id)
    {
        return PinnedMessages::where(['chat_room_id' => $chat_room_id])->get();
    }

    public function deletePinnedMessage($chat_room_id)
    {
        $removed_wanna_be = PinnedMessages::where(['chat_room_id' => $chat_room_id]);
        
        $removed_wanna_be->delete();

        error_log($removed_wanna_be->first());
        
        broadcast(new PinMessage($chat_room_id))->toOthers();
        return "Pinned message deleted!";
    }
}