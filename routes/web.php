<?php

use App\Http\Controllers\BidController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashBoardController::class, 'index'])->name('dashboard');
    Route::get('bids-show/{id}', [DashBoardController::class, 'bidsShow'])->name('bids-show');
    Route::post('place-bid', [BidController::class, 'placeBid'])->name('place-bid');
    Route::get('/chat/users', [ChatController::class, 'listUsers'])->name('chat-users');
    Route::get('/chat/{bidderId}', [ChatController::class, 'showChat'])->name('chat-show');
    Route::get('/chat', [ChatController::class, 'showChatForBidder'])->name('chat-showForBidder');
    Route::post('send-message', [ChatController::class, 'sendMessage'])->name('send-message');
});

Route::middleware('admin', 'auth')->group(function () {
    Route::resource('/products', ProductController::class);
});




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
