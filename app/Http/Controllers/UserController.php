<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index() {
        if(!auth()->user()){
            return redirect()->route('login');
        }
        $users = User::latest()->paginate(10);
        return view('user.index', compact('users'));
    }
}