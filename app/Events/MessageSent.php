<?php

namespace App\Events;

use App\Models\Messages;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $receiverId;

    public function __construct(Messages $message, $receiverId) 
    {
        $this->message = $message;
        $this->receiverId = $receiverId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->conversation_id),
            new PrivateChannel('user.' . $this->receiverId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message'=>[
                'id'=>$this->message->id,
                'conversation_id'=>$this->message->conversation_id,
                'sender_id'=>$this->message->sender_id,
                'ciphertext'=>$this->message->ciphertext,
                'iv'=>$this->message->iv,
                'created_at'=>$this->message->created_at->format('H:i'),
            ]
        ];
    }
}
