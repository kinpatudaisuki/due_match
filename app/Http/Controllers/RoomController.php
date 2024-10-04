<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Message;

class RoomController extends Controller
{
    public function index($room_id) {
        if(!auth()){
            return redirect()->route('login');
        }

        //roomにいるユーザー一覧
        $room = Room::find($room_id);
        $users = $room->users;

        //roomにあるコメント一覧
        $messages = Message::where('room_id', $room_id)->get();
        return view('room.index', compact('users', 'messages'));
    }
}
