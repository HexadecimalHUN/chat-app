<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BlockUnblockUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat_room_id;
    public $is_blocked;
    public $blocked_by;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($chat_room_id, $is_blocked, $blocked_by)
    {
        $this -> chat_room_id = $chat_room_id;
        $this -> is_blocked = $is_blocked;
        $this -> blocked_by = $blocked_by;
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