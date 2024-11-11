<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessageNotification;
use App\Jobs\SendNotificationEmail;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function show($room_id) {

        $currentUserId = Auth::id();

        // ログインユーザーがいない場合はリダイレクトまたはエラーメッセージを表示
        if (!$currentUserId) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        $currentUser = User::findOrFail($currentUserId);

        // 招待するために全てのユーザーデータを取得
        $all_users = User::all();

        // roomにいるユーザー一覧
        $room = Room::find($room_id);
        $room_users = $room->users;

        // 現在のログインユーザーがブロックしているユーザーIDリスト
        $blockedUsers = $currentUser->blockedUsers()->pluck('blocked_id')->toArray();

        // room内のコメント一覧を取得し、ブロックしたユーザーのメッセージを除外
        $messages = Message::where('room_id', $room_id)
            ->whereNotIn('user_id', $blockedUsers)  // 自分がブロックしたユーザーを除外
            ->get();

        return view('room.show', compact('all_users', 'room_users', 'messages', 'room_id'));
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
    public function sendMessage(Request $request, $roomId) {
        $request->validate([
            'body' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif'
        ]);

        // メッセージと画像が両方nullの場合はエラー
        if (!$request->filled('body') && !$request->hasFile('image')) {
            return response()->json(['error' => 'メッセージまたは画像が必要です'], 422);
        }

        $room = Room::findOrFail($roomId);
        $currentUserId = Auth::id();
        $messageBody = $request->input('body');
        if(empty($messageBody)){
            $messageBody = "新しい画像を投稿しました。";
        }
        $imagePath = null;

        // 画像がアップロードされた場合の処理
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            if (app()->environment('production')) {
                // S3にアップロード
                $imagePath = $image->store('messages', 's3');
            } else {
                // ローカルストレージにアップロード
                $imagePath = $image->store('messages', 'public');
            }
        }

        // メッセージを作成
        $message = new Message();
        $message->body = $messageBody;
        $message->user_id = $currentUserId;
        $message->room_id = $room->id;
        $message->image = $imagePath;
        $message->save();

        // ログインユーザーがブロックしている、またはブロックされているユーザーを取得
        $currentUser = User::findOrFail($currentUserId);
        $blockedUsers = $currentUser->blockedUsers()->pluck('blocked_id')->toArray();
        $blockedByUsers = $currentUser->blockers()->pluck('blocker_id')->toArray();
        $excludedUsers = array_merge($blockedUsers, $blockedByUsers);

        // 他のメンバーに通知を送信（非同期処理）
        $roomUsers = $room->users()
            ->where('user_id', '!=', $currentUserId)
            ->whereNotIn('user_id', $excludedUsers) // ブロック関係のユーザーを除外
            ->get();

        $senderName = Auth::user()->name;

        foreach ($roomUsers as $user) {
            SendNotificationEmail::dispatch($user->email, $messageBody, $senderName);
        }

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

    public function leave($room_id) {
        $room = Room::findOrFail($room_id);
        $user = Auth::user();

        // ユーザーをトークルームから削除
        $room->users()->detach($user->id);

        // 退出メッセージを保存
        $message = new Message();
        $message->room_id = $room_id;
        $message->user_id = null; // 退出メッセージは特定のユーザーに属さない
        $message->body = $user->name . ' が退出しました';
        $message->save();

        return redirect()->route('room.index')->with('success', 'トークルームから退会しました');
    }

}
