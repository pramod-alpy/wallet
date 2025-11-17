<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;   // <-- REQUIRED
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Event triggered when a user transfers money.
 * Broadcasts the updated balance and transaction details to the specific user.
 */

class MoneyTransferred implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $newBalance;
    public $transaction;

      /**
     * Create a new event instance.
     *
     * @param int $userId The ID of the user to broadcast to
     * @param float $newBalance The updated balance of the user
     * @param array $transaction The transaction data   
     *  
     */

    public function __construct($userId, $newBalance, $transaction)
    {      
        $this->userId = $userId;
        $this->newBalance = $newBalance;
        $this->transaction = $transaction;
    }

     /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\PrivateChannel
     */


    public function broadcastOn()
    {        
        return new PrivateChannel('user.' . $this->userId);
    }

    /**
     * Get the event name to broadcast as.
     *
     * @return string
     */

    public function broadcastAs()
    {        
        return 'balance.updated';
    }
}
