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
    public function show($room_id) {
        if(!Auth::user()){
            return redirect()->route('login');
        }

        // 全てのユーザー
        $all_users = User::all();

        // roomにいるユーザー一覧
        $room = Room::find($room_id);
        $room_users = $room->users;

        // roomにあるコメント一覧
        $messages = Message::where('room_id', $room_id)->get();
        return view('room.show', compact('all_users', 'room_users', 'messages'));
    }

    public function index() {

        if(!Auth::user()){
            return redirect()->route('login');
        }

        // ログインユーザーが所属しているルームを取得
        $rooms = Auth::user()->rooms;
        
        return view('room.index', compact('rooms'));
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
            $room->users()->syncWithoutDetaching($userIds);

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

    // メッセージ送信
    public function sendMessage(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);

        // メッセージを作成
        $message = new Message();
        $message->body = $request->input('message');
        $message->user_id = Auth::id();
        $message->room_id = $room->id;
        $message->save();

        return response()->json(['message' => 'メッセージが送信されました']);
    }

    public function inviteUser(Request $request, $roomId) {
        // 招待するユーザーIDをリクエストから取得
        $userId = $request->input('user_id');

        // 指定されたルームを取得
        $room = Room::findOrFail($roomId);

        // ユーザーが既にルームに存在するかチェック
        if ($room->users()->where('user_id', $userId)->exists()) {
            return response()->json([
                'message' => 'そのユーザーは既にルーム内にいます'
            ], 400);
        }

        // ユーザーをルームに追加
        $room->users()->syncWithoutDetaching($userId);

        return response()->json([
            'message' => 'ユーザーを招待しました',
            'room_id' => $room->id
        ], 200);
    }
}
