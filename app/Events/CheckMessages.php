<?php

namespace App\Events;

use App\Models\ChatMessage;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Expr\Cast\Array_;

class CheckMessages implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
      * Message details
      *
      * @param int $chat_room_id
      */
    public $chat_room_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat_room_id)
    {
        $this->chat_room_id = $chat_room_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // shoud create a chanel with the other details
        return new PrivateChannel('chat.'. $this->chat_room_id);
    }
}