<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForumClear implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $level_id;

    /**
     * Create a new event instance.
     */
    public function __construct($level_id)
    {
        $this->level_id = $level_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('forum-channel');
    }
    
    /**
     * Broadcast event forum message 
     */
    public function broadcastAs()
    {
        return 'forum-clear';
    }
}
