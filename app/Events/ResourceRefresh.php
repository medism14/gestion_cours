<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceRefresh implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $resource;

    /**
     * Create a new event instance.
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('resource-channel');
    }
    
    /**
     * 
     */
    public function broadcastAs()
    {
        return 'resource-refresh';
    }
}