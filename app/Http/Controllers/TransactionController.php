<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Events\MoneyTransferred;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Retrieves the authenticated user's current wallet balance and transaction history.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing balance and transactions
     */
    public function index()
    {       
        $user = Auth::user();
        return response()->json([
            'balance' => $user->balance,
            'transactions' => Transaction::with(['sender', 'receiver'])
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)
                      ->orWhere('receiver_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }

    /**
     * Adds funds to the authenticated user's wallet.
     * The updated balance is immediately reflected after a successful transfer.
     * @param \Illuminate\Http\Request $request The HTTP request containing the amount to add
     * @return \Illuminate\Http\JsonResponse JSON response with success message and updated balance      
     */

    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $user = Auth::user();
        $user->balance += $request->amount;
        $user->save();

        return response()->json([
            'message' => 'Funds added successfully!',
            'balance' => $user->balance,
        ]);
    }

    /**
     *  Handles money transfer from the authenticated user to another user
     *  @param \Illuminate\Http\Request $request The HTTP request containing receiver ID and amount
     *  @return \Illuminate\Http\JsonResponse JSON response with success or error message
     *  Input the User ID and the amount need to Transfer
     *  After Succesful transfer it balance amount from Sender will be debited authomatically 
     *  and Receicer Wallet balances and transaction history will automatically
     *  updated using Broadcast Functionality
     *  
     */

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);
        $sender = Auth::user();
        $receiver = User::find($request->receiver_id);
        $amount = $request->amount;
        $commission = $amount * 0.015;
        $totalDebit = $amount + $commission;
        if ($sender->balance < $totalDebit) {
            return response()->json([
                'message' => 'Insufficient balance to complete the transaction, add funds to your account',
            ], 400);
        }
        $sender->balance -= $totalDebit;
        $receiver->balance += $amount;
        $sender->save();
        $receiver->save();

        $transaction = Transaction::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => $amount,
            'commission_fee' => $commission,
        ]);      

        $transaction = Transaction::with(['sender', 'receiver'])->find($transaction->id);
        
        broadcast(new MoneyTransferred($sender->id, $sender->balance, $transaction));      

        broadcast(new MoneyTransferred($receiver->id, $receiver->balance, $transaction));

        return response()->json([
            'message' => 'Transfer successful',
            'transaction' => $transaction,
        ]);
    }   

    
}
