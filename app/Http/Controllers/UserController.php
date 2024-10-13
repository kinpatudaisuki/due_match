<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Rating;
use App\Models\Block;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index() {
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

        // ユーザー一覧を取得（フォーマットも一緒に取得）
        // 自分がブロックしたユーザーは除外
        $users = User::with('formats')
            ->latest()
            ->paginate(10);

        return view('user.index', compact('users', 'blockedUsers', 'blockers'));
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
