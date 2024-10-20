<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\Format;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        // formatsテーブルから全てのフォーマットを取得
        $formats = Format::all();

        return view('profile.edit', [
            'user' => $request->user(),
            'formats' => $formats,
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
                // 本番環境ならS3から削除、そうでなければローカルのストレージから削除
                if (app()->environment('production')) {
                    Storage::disk('s3')->delete($user->image);
                } else {
                    Storage::disk('public')->delete($user->image);
                }
            }

            try {

                // 新しい画像を保存し、そのパスを設定
                $path = $request->file('image')->store('images', 's3');
                $user->image = $path;

            } catch (\Exception $e) {

                \Log::error('S3 upload error: ' . $e->getMessage());

                // エラーメッセージをセッションに保存してリダイレクト
                return Redirect::route('profile.edit')->with('error', '画像のアップロードに失敗しました。');

            }
        }

        // ユーザー情報を保存
        $user->save();

        // フォーマットが選択されているかチェック
        if ($request->has('formats') && !empty($request->input('formats'))) {
            // カンマ区切りの文字列を配列に変換
            $formatsArray = explode(',', $request->input('formats'));

            // フォーマットが選択されている場合は同期
            $user->formats()->sync($formatsArray);
        } else {
            // フォーマットが未選択の場合、すべてのフォーマットを解除
            $user->formats()->detach();
        }

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

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
