<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $conversationId;
    public $receiverId;
    public $isLastMsg;
    public $lastMsg;

    public function __construct($messageId, $conversationId, $receiverId, $isLastMsg, $lastMsg)
    {
        $this->messageId = $messageId;
        $this->conversationId = $conversationId;
        $this->receiverId = $receiverId;
        $this->isLastMsg = $isLastMsg;
        $this->lastMsg = $lastMsg;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->conversationId),
            new PrivateChannel('user.' . $this->receiverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.deleted';
    }
    
    public function broadcastWith(): array
    {
        return [
            'messageId' => $this->messageId,
            'conversationId' => $this->conversationId,
            'isLastMsg' => $this->isLastMsg,
            'lastMsg' => $this->lastMsg,
        ];
    }
}
