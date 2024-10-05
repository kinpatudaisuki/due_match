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
        $users = User::latest()->paginate(10);
        return view('user.index', compact('users'));
    }
}
