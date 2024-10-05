<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function index($room_id) {
        if(!auth()->user()){
            return redirect()->route('login');
        }

        // roomにいるユーザー一覧
        $room = Room::find($room_id);
        $users = $room->users;

        // roomにあるコメント一覧
        $messages = Message::where('room_id', $room_id)->get();
        return view('room.index', compact('users', 'messages'));
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {
            // Roomを作成する
            $room = Room::create();

            // 紐づくUserを登録する
            $userIds = $request->input('user_ids');

            // 中間テーブルにデータを登録
            $room->users()->attach($userIds);

            DB::commit();

            return response()->json(['message' => 'Room created successfully!', 'room_id' => $room->id], 201);
        } catch (\Exception $e) {
            // エラー発生時はロールバック
            DB::rollBack();
            // エラーログに記録
            \Log::error('Room creation failed: ' . $e->getMessage());

            // 500エラーとともに詳細メッセージを返す
            return response()->json(['error' => 'Room creation failed', 'details' => $e->getMessage()], 500);
        }
    }
}
