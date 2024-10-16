<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    // ユーザーをブロック
    public function block($block_user_id) {

        $currentUserId = Auth::id();
        $currentUser = User::findOrFail($currentUserId);
        if ($currentUser->hasBlocked($block_user_id)) {
            return response()->json(['message' => '既にブロックされています'], 400);
        }

        Block::create([
            'blocker_id' => Auth::user()->id,
            'blocked_id' => $block_user_id,
        ]);

        return response()->json(['message' => 'ブロックしました']);
    }

    // ユーザーのブロックを解除
    public function unblock($unblock_user_id) {
        $block = Block::where('blocker_id', Auth::user()->id)
                      ->where('blocked_id', $unblock_user_id)
                      ->first();

        if (!$block) {
            return response()->json(['message' => 'そのユーザーはブロックされていません'], 400);
        }

        $block->delete();

        return response()->json(['message' => 'ブロック解除しました']);
    }

    public function index() {
        $currentUserId = Auth::id();

        // ログインユーザーがいない場合はリダイレクトまたはエラーメッセージを表示
        if (!$currentUserId) {
            return redirect()->route('login')->with('error', 'ログインが必要です。');
        }

        $currentUser = User::findOrFail($currentUserId);

        // ログインユーザーがブロックしているユーザーのデータを取得する
        $blockedUsers = $currentUser->blockedUsers()->paginate(10);

        // blocked_id を使ってユーザー情報を取得
        $blockedUserData = $blockedUsers->map(function ($block) {
            return User::find($block->blocked_id);
        });

        return view('block.index', compact('blockedUserData'));
    }

}
