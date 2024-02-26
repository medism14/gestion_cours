<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForumMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $forum, $actualiser;

    /**
     * Create a new event instance.
     */
    public function __construct($forum, $actualiser)
    {
        $this->forum = $forum;
        $this->actualiser = $actualiser;
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
        return 'forum-new-message';
    }
}