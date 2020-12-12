<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendEmail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

   public $recipient;
   public $content;

   /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($recipient, $emailContent)
    {
        //
       $this->recipient = $recipient;
       $this->content = $emailContent;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
