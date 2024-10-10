<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
}
