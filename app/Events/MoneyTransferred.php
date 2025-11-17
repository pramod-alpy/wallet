<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;   // <-- REQUIRED
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MoneyTransferred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $newBalance;
    public $transaction;

    public function __construct($userId, $newBalance, $transaction)
    {      
        $this->userId = $userId;
        $this->newBalance = $newBalance;
        $this->transaction = $transaction;
    }

    public function broadcastOn()
    {        
        return new PrivateChannel('user.' . $this->userId);
    }

    public function broadcastAs()
    {        
        return 'balance.updated';
    }
}
