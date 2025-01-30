<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rating;
use App\Models\Block;
use App\Models\Format;
use App\Models\Friend;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request) {
        // ログインユーザーの確認
        $currentUserId = Auth::id();

        // ログインしている場合のみユーザー情報を取得
        $blockedUsers = [];
        $blockers = [];
        if ($currentUserId) {
            $currentUser = User::findOrFail($currentUserId);
            $blockedUsers = $currentUser->blockedUsers()->pluck('blocked_id')->toArray();
            $blockers = $currentUser->blockers()->pluck('blocker_id')->toArray();
        }

        // 検索ユーザー名を取得
        $userName = $request->input('userName');
        // 検索ユーザー名を取得
        $intro = $request->input('intro');
        // フォーマットIDを取得
        $formatId = $request->input('format_id');
        // エリアを取得
        $area = $request->input('area');

        // ユーザーを取得し、検索キーワードとフォーマットでフィルタリング
        $query = User::with('formats');

        if ($userName) {
            $query->where('name', 'like', '%' . $userName . '%');
        }

        if ($intro) {
            $query->where('introduction', 'like', '%' . $intro . '%');
        }

        if ($formatId) {
            $query->whereHas('formats', function ($q) use ($formatId) {
                $q->where('formats.id', $formatId);
            });
        }

        // エリアでフィルタリング
        if ($area) {
            $query->where('area', $area);
        }

        // ページネーションを適用してユーザーを取得
        $users = $query->latest()->paginate(10);

        // フォーマットをデータベースから取得
        $formats = Format::pluck('name', 'id'); // フォーマット名を値、IDをキーとして取得

        // 全都道府県のリスト
        $areas = [
            '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
            '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
            '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
            '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
            '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
            '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
            '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
        ];

        return view('user.index', compact('users', 'blockedUsers', 'blockers', 'userName', 'formatId', 'formats', 'areas'));
    }

    public function show($user_id) {

        // 閲覧するユーザーのデータ
        $user_data = User::with('formats')->findOrFail($user_id);

        $currentUserId = Auth::id();
        $isRated = false;
        $hasBlocked = false;
        $isBlocked = false;
        $isFriendRequestPending = false;
        $isReceivedFriendRequest = false;
        $isFriend = false;

        if ($currentUserId) {

            $currentUser = User::findOrFail($currentUserId);

            // ログインユーザーがこのユーザーを評価済みか確認
            $isRated = Rating::where('rater_id', $currentUserId)
                            ->where('rated_id', $user_id)
                            ->exists();

            // ログインユーザーがこのユーザーをブロックしているか確認
            $hasBlocked = $currentUser->hasBlocked($user_id);

            // ログインユーザーがこのユーザーにブロックされているか確認
            $isBlocked = $currentUser->isBlockedBy($user_id);

            // ログインユーザーがこのユーザーへフレンド申請済みか確認
            $isFriendRequestPending = Friend::isPendingRequest($currentUserId, $user_id);

            // ログインユーザーがこのユーザーからフレンド申請受け取り済みか確認
            $isReceivedFriendRequest = Friend::hasReceivedFriendRequest($currentUserId, $user_id);

            // ログインユーザーがこのユーザーとフレンドか確認
            $isFriend = Friend::isFriend($currentUserId, $user_id);

        }

        return view('user.show', compact('user_data', 'isRated', 'hasBlocked', 'isBlocked', 'isFriendRequestPending', 'isReceivedFriendRequest', 'isFriend'));
    }

}
