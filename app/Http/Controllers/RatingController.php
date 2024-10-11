<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request) {
        // リクエストから評価情報を取得
        $validated = $request->validate([
            'rated_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:3',
        ]);

        // 評価を保存
        $rating = Rating::updateOrCreate(
            [
                'rater_id' => Auth::user()->id,
                'rated_id' => $validated['rated_id']
            ],
            ['rating' => $validated['rating']]
        );

        // ユーザーの合計評価数を更新
        $ratedUser = User::find($validated['rated_id']);
        $ratedUser->total_rate += $validated['rating']; // 星の数だけ加算
        $ratedUser->save();

        return response()->json(['message' => '評価が完了しました']);
    }
}
