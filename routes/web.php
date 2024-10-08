<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('user/index', [UserController::class, 'index'])->name('user.index');

Route::post('room/store', [RoomController::class, 'store'])->name('room.store');

Route::get('room/show/{room}', [RoomController::class, 'show'])->name('room.show');

Route::get('room/index', [RoomController::class, 'index'])->name('room.index');

// メッセージ送信
Route::post('/room/{roomId}/send_message', [RoomController::class, 'sendMessage'])->name('room.sendMessage');

//ユーザー招待
Route::post('/room/{roomId}/invite', [RoomController::class, 'inviteUser']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
