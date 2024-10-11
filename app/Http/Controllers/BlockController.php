<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    // ユーザーをブロック
    public function block($block_user_id)
    {
        if (Auth::user()->hasBlocked($block_user_id)) {
            return response()->json(['message' => '既にブロックされています'], 400);
        }

        Block::create([
            'blocker_id' => Auth::user()->id,
            'blocked_id' => $block_user_id,
        ]);

        return response()->json(['message' => 'ブロックしました']);
    }

    // ユーザーのブロックを解除
    public function unblock($unblock_user_id)
    {
        $block = Block::where('blocker_id', Auth::user()->id)
                      ->where('blocked_id', $unblock_user_id)
                      ->first();

        if (!$block) {
            return response()->json(['message' => 'そのユーザーはブロックされていません'], 400);
        }

        $block->delete();

        return response()->json(['message' => 'ブロック解除しました']);
    }
}
