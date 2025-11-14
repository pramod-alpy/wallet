<?php



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Routing\Middleware\SubstituteBindings;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
/*
Route::middleware([
    SubstituteBindings::class,
])->group(function () {*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/balance', [TransactionController::class, 'balance']);
    Route::post('/add-funds', [TransactionController::class, 'addFunds']);
});




