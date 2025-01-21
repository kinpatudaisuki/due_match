<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Friend;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendFriendRequestEmail;

class FriendController extends Controller
{
    public function index() {
        $user = Auth::user();

        return view('friend.index', [
            'acceptedFriends' => $user->acceptedFriends()->get(), // フレンドリスト
            'pendingFriends' => $user->pendingRequests()->get(), // フレンド申請中
            'approvalWaiting' => $user->approvalWaiting()->get(), // 承認待機中
        ]);
    }

    public function sendRequest(Request $request, $userId) {
        $user = Auth::user();
        $recipient = User::findOrFail($userId);

        // 既にフレンド申請があるか確認
        if (Friend::where('user_id', $user->id)->where('friend_id', $userId)->exists()) {
            return response()->json(['success' => false, 'message' => 'すでに申請済みです。']);
        }

        // フレンド申請を作成
        Friend::create([
            'user_id' => $user->id,
            'friend_id' => $userId,
            'status' => 'pending'
        ]);

        // フレンド申請通知をメールで送信（非同期処理）
        SendFriendRequestEmail::dispatch($recipient->email, $user->name);

        return response()->json(['success' => true, 'message' => 'フレンド申請を送信しました。']);
    }

    public function approveFriendRequest($userId) {
        $currentUserId = Auth::id();

        // 受け取ったフレンド申請を検索
        $friendRequest = Friend::where('user_id', $userId)
                               ->where('friend_id', $currentUserId)
                               ->where('status', 'pending')
                               ->first();

        if ($friendRequest) {
            // ステータスを 'accept' に更新
            $friendRequest->update(['status' => 'accept']);

            // user_id と friend_id を入れ替えて新しいデータを作成
            Friend::create([
                'user_id' => $currentUserId,
                'friend_id' => $userId,
                'status' => 'accept'
            ]);

            return response()->json(['success' => true, 'message' => 'フレンド申請を承認しました。']);
        }

        return response()->json(['success' => false, 'message' => 'フレンド申請が見つかりませんでした。']);
    }

    public function denyFriendRequest($userId) {
        $currentUserId = Auth::id();

        // フレンド申請を検索（statusがpendingのもの）
        $friendRequest = Friend::where('user_id', $userId)
                                ->where('friend_id', $currentUserId)
                                ->where('status', 'pending')
                                ->first();

        if ($friendRequest) {
            // 申請を削除
            $friendRequest->delete();

            return response()->json(['success' => true, 'message' => 'フレンド申請を拒否しました。']);
        }

        return response()->json(['success' => false, 'message' => 'フレンド申請が見つかりませんでした。']);
    }

    public function removeFriend($userId) {
        $currentUserId = Auth::id();

        if (!Friend::isFriend($currentUserId, $userId)) {
            return response()->json(['success' => false, 'message' => 'フレンド関係が見つかりませんでした。']);
        }

        // フレンド関係を削除
        $deleted = Friend::where(function ($query) use ($userId, $currentUserId) {
                $query->where('user_id', $currentUserId)
                      ->where('friend_id', $userId);
            })
            ->orWhere(function ($query) use ($userId, $currentUserId) {
                $query->where('user_id', $userId)
                      ->where('friend_id', $currentUserId);
            })
            ->where('status', 'accept')
            ->delete();

        return response()->json(['success' => $deleted, 'message' => $deleted ? 'フレンドを解除しました。' : 'フレンド解除に失敗しました。']);
    }

    public function blockFriendship($userId) {

        // まずフレンド申請の削除を試みる
        $response = $this->denyFriendRequest($userId);
        $responseData = json_decode($response->getContent(), true);

        // フレンド申請が見つからなかった場合、フレンド関係の削除を試みる
        if (!$responseData['success']) {
            $response = $this->removeFriend($userId);
        }

        return $response;
    }

}
