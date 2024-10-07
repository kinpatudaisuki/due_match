<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 現在のユーザーを取得
        $user = $request->user();

        // バリデーション済みのデータをユーザーにセット
        $user->fill($request->validated());

        // メールアドレスが変更された場合、認証情報をリセット
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 画像がアップロードされた場合の処理
        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }

            // 新しい画像を保存し、そのパスを設定
            $path = $request->file('image')->store('images', 'public');
            $user->image = $path;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
