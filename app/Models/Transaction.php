<?php
namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'amount', 'commission_fee'];

    /**
     * Sender User details fetch using sender_id from transaction table
     */
    public function sender() { 
        return $this->belongsTo(User::class, 'sender_id'); 
    }

     /**
     * Receiver User details fetch using receiver_id from transaction table
     */
    public function receiver() { 
        return $this->belongsTo(User::class, 'receiver_id'); 
    }
}
