<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rating;
use App\Models\Block;
use App\Models\Format;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index(Request $request) {
        // ログインユーザーの確認
        $currentUserId = Auth::id();

        // ログインしていない場合、ログインページにリダイレクト
        if (!$currentUserId) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        // ログインユーザーの情報を取得
        $currentUser = User::findOrFail($currentUserId);

        // ログインユーザーがブロックしたユーザーと、ブロックされているユーザーを取得
        $blockedUsers = $currentUser->blockedUsers()->pluck('blocked_id')->toArray();
        $blockers = $currentUser->blockers()->pluck('blocker_id')->toArray();

        // 検索キーワードを取得
        $keyword = $request->input('keyword');
        // フォーマットIDを取得
        $formatId = $request->input('format_id');

        // ユーザーを取得し、検索キーワードとフォーマットでフィルタリング
        $query = User::with('formats');

        if ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        if ($formatId) {
            $query->whereHas('formats', function ($q) use ($formatId) {
                $q->where('formats.id', $formatId);
            });
        }

        // ページネーションを適用してユーザーを取得
        $users = $query->latest()->paginate(10);

        // フォーマットをデータベースから取得
        $formats = Format::pluck('name', 'id'); // フォーマット名を値、IDをキーとして取得

        return view('user.index', compact('users', 'blockedUsers', 'blockers', 'keyword', 'formatId', 'formats'));
    }

    public function show($user_id) {

        // 閲覧するユーザーのデータ
        $user_data = User::with('formats')->findOrFail($user_id);

        // 現在のログインユーザーのIDを取得
        $currentUserId = Auth::id();

        // ログインユーザーがいない場合はリダイレクトまたはエラーメッセージを表示
        if (!$currentUserId) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        // 現在のログインユーザーをUserモデルから取得
        $currentUser = User::findOrFail($currentUserId);

        // ログインユーザーがこのユーザーを評価済みか確認
        $isRated = Rating::where('rater_id', $currentUserId)
                          ->where('rated_id', $user_id)
                          ->exists();

        // ログインユーザーがこのユーザーをブロックしているか確認
        $hasBlocked = $currentUser->hasBlocked($user_id);

        // ログインユーザーがこのユーザーにブロックされているか確認
        $isBlocked = $currentUser->isBlockedBy($user_id);

        return view('user.show', compact('user_data', 'isRated', 'hasBlocked', 'isBlocked'));
    }

}
