<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/test-log', function () {
    Log::info('Test log message');
    return 'Log written';
});

Route::get('user/index', [UserController::class, 'index'])->name('user.index');

Route::get('user/show/{user_id}', [UserController::class, 'show'])->name('user.show');

Route::post('rate', [RatingController::class, 'store'])->middleware('auth');

Route::post('room/store', [RoomController::class, 'store'])->name('room.store');

Route::get('room/show/{room_id}', [RoomController::class, 'show'])->name('room.show');

Route::get('room/index', [RoomController::class, 'index'])->name('room.index');

Route::post('/room/leave/{room}', [RoomController::class, 'leave'])->name('room.leave');

// メッセージ送信
Route::post('/room/{room_id}/send_message', [RoomController::class, 'sendMessage'])->name('room.sendMessage');

//ユーザー招待
Route::post('/room/{room_id}/invite', [RoomController::class, 'inviteUser']);

Route::get('/block/index', [BlockController::class, 'index'])->name('block.index');

//ブロックとブロック解除
Route::post('/block/{user_id}', [BlockController::class, 'block'])->middleware('auth');
Route::post('/unblock/{user_id}', [BlockController::class, 'unblock'])->middleware('auth');

//問い合わせの作成と保存
Route::get('contact/create', [ContactController::class, 'create'])->name('contact.create');
Route::post('contact/store', [ContactController::class, 'store'])->name('contact.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
