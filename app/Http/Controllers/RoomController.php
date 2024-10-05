<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function index($room_id) {
        if(!Auth::user()){
            return redirect()->route('login');
        }

        // roomにいるユーザー一覧
        $room = Room::find($room_id);
        $users = $room->users;

        // roomにあるコメント一覧
        $messages = Message::where('room_id', $room_id)->get();
        return view('room.index', compact('users', 'messages'));
    }

    public function store(Request $request) {

        DB::beginTransaction();

        try {
            // 紐づくUserのIDを取得
            $userIds = $request->input('user_ids');

            // ログインユーザー
            $currentUser = Auth::user();

            // 全てのユーザーが既に所属しているルームが存在するか確認
            $existingRoom = Room::whereHas('users', function ($query) use ($currentUser) {
                $query->where('user_id', $currentUser->id);
            })->whereHas('users', function ($query) use ($userIds) {
                $query->whereIn('user_id', $userIds);
            }, '=', count($userIds))->first();

            if ($existingRoom) {
                // 既存のルームが見つかった場合、そのルームIDを返す
                return response()->json([
                    'room_id' => $existingRoom->id
                ], 200);
            }

            // ルームが存在しない場合、新規作成
            $room = Room::create();

            // 中間テーブルにデータを登録
            $room->users()->attach($userIds);

            DB::commit();

            return response()->json([
                'message' => 'ルームを作成しました',
                'room_id' => $room->id
            ], 201);

        } catch (\Exception $e) {
            // エラー発生時はロールバック
            DB::rollBack();
            // エラーログに記録
            Log::error('Room creation failed: ' . $e->getMessage());

            // 500エラーとともに詳細メッセージを返す
            return response()->json([
                'error' => 'Room creation failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
