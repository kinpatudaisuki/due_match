<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
        if(!Auth::user()){
            return redirect()->route('login');
        }

        // ユーザーを取得し、フォーマットも同時に取得する
        $users = User::with('formats')->latest()->paginate(10);

        return view('user.index', compact('users'));
    }

    public function show($user_id) {
        $user_data = User::with('formats')->findOrFail($user_id);

        // 現在のログインユーザー
        $currentUser = Auth::user();

        // ログインユーザーがこのユーザーを評価済みか確認
        $isRated = Rating::where('rater_id', $currentUser->id)
                          ->where('rated_id', $user_id)
                          ->exists();

        return view('user.show', compact('user_data', 'isRated'));
    }
}
