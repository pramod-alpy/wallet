<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MoneyTransferred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $transaction;
    public $senderId;
    public $receiverId;

    public function __construct($transaction)
    {
        $this->transaction = $transaction;
        $this->senderId = $transaction['sender_id'];
        $this->receiverId = $transaction['receiver_id'];
    }

    public function broadcastOn()
    {
        // Broadcast to both sender and receiver
        return [
            new \Illuminate\Broadcasting\PrivateChannel('user.' . $this->receiverId),
            new \Illuminate\Broadcasting\PrivateChannel('user.' . $this->senderId),
        ];
    }

    public function broadcastAs()
    {
        return 'money.transferred';
    }
}
