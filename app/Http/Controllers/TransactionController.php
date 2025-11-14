<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Events\MoneyTransferred;

class TransactionController extends Controller
{
    public function index()
    {
       /* $user = Auth::user();      
        return Auth::user()->transactions; // or Transaction::where('user_id', Auth::id())->get();*/

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
            'message' => 'Insufficient balance to complete the transaction.',
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

    // 🔥 Fire real-time event
  //  broadcast(new MoneyTransferred($transaction))->toOthers();
  broadcast(new MoneyTransferred($transaction));
   // broadcast(new MoneyTransferred($transaction))->toOthers();
//dd($transaction);

    return response()->json([
        'message' => 'Transfer successful',
        'transaction' => $transaction,
    ]);
}
   


   /* public function store(Request $request)
    {
        // Example: transfer money
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $sender = Auth::user();
        $receiver = User::find($request->receiver_id);
        $amount = $request->amount;
        $commission = $amount * 0.015;
        $totalDebit = $amount + $commission;

        // ✅ Check if sender has enough balance
        if ($sender->balance < $totalDebit) {
            return response()->json([
                'message' => 'Insufficient balance to complete the transaction.',
            ], 400);
        }       

        // Deduct from sender, add to receiver
        $sender->balance -= $totalDebit;
        $receiver->balance += $request->amount;
        $sender->save();
        $receiver->save();

        // Record transaction
        $transaction = Transaction::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => $request->amount,
            'commission_fee' => $commission,
        ]);

        return response()->json($transaction);
    }
    */

    public function balance()
    {
        return response()->json(['balance' => Auth::user()->balance]);
    }
}
