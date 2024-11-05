<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- エリア -->
        <div class="mt-4">
            <x-input-label for="area" :value="__('都道府県')" />
            <select id="area" name="area" class="block mt-1 w-full" required>
                <option value="" disabled selected>都道府県を選択してください</option>
                <option value="北海道">北海道</option>
                <option value="青森県">青森県</option>
                <option value="岩手県">岩手県</option>
                <option value="宮城県">宮城県</option>
                <option value="秋田県">秋田県</option>
                <option value="山形県">山形県</option>
                <option value="福島県">福島県</option>
                <option value="茨城県">茨城県</option>
                <option value="栃木県">栃木県</option>
                <option value="群馬県">群馬県</option>
                <option value="埼玉県">埼玉県</option>
                <option value="千葉県">千葉県</option>
                <option value="東京都">東京都</option>
                <option value="神奈川県">神奈川県</option>
                <option value="新潟県">新潟県</option>
                <option value="富山県">富山県</option>
                <option value="石川県">石川県</option>
                <option value="福井県">福井県</option>
                <option value="山梨県">山梨県</option>
                <option value="長野県">長野県</option>
                <option value="岐阜県">岐阜県</option>
                <option value="静岡県">静岡県</option>
                <option value="愛知県">愛知県</option>
                <option value="三重県">三重県</option>
                <option value="滋賀県">滋賀県</option>
                <option value="京都府">京都府</option>
                <option value="大阪府">大阪府</option>
                <option value="兵庫県">兵庫県</option>
                <option value="奈良県">奈良県</option>
                <option value="和歌山県">和歌山県</option>
                <option value="鳥取県">鳥取県</option>
                <option value="島根県">島根県</option>
                <option value="岡山県">岡山県</option>
                <option value="広島県">広島県</option>
                <option value="山口県">山口県</option>
                <option value="徳島県">徳島県</option>
                <option value="香川県">香川県</option>
                <option value="愛媛県">愛媛県</option>
                <option value="高知県">高知県</option>
                <option value="福岡県">福岡県</option>
                <option value="佐賀県">佐賀県</option>
                <option value="長崎県">長崎県</option>
                <option value="熊本県">熊本県</option>
                <option value="大分県">大分県</option>
                <option value="宮崎県">宮崎県</option>
                <option value="鹿児島県">鹿児島県</option>
                <option value="沖縄県">沖縄県</option>
            </select>
            <x-input-error :messages="$errors->get('area')" class="mt-2" />
        </div>

        <!-- フォーマットボタン -->
        <div class="mt-4">
            <x-input-label for="formats" :value="__('フォーマットを選択 (任意)')" />
            <div class="mt-2 flex gap-4">
                <button type="button" class="format-btn" data-format-id="1">オリジナル</button>
                <button type="button" class="format-btn" data-format-id="2">アドバンス</button>
                <button type="button" class="format-btn" data-format-id="3">その他</button>
            </div>

            <!-- 隠しフィールド -->
            <input type="hidden" name="formats" id="selected-formats" value="{{ old('formats') }}">

            <x-input-error class="mt-2" :messages="$errors->get('formats')" />
        </div>


        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // フォーマットの選択結果を格納する隠しフィールドを取得
        const selectedFormatsInput = document.getElementById('selected-formats');

        // 隠しフィールドに既に値が入っていれば、それを配列として扱う。なければ空の配列を用意する。
        let selectedFormats = selectedFormatsInput.value ? selectedFormatsInput.value.split(',') : [];

        // 各フォーマットボタンに対して、クリックイベントのリスナーを設定
        document.querySelectorAll('.format-btn').forEach(function (btn) {
            // 各ボタンに関連付けられたフォーマットIDを取得
            const formatId = btn.getAttribute('data-format-id');

            // ボタンがクリックされた時の処理
            btn.addEventListener('click', function () {
                // もしボタンが「アクティブ」状態なら、それを解除して選択されたフォーマットから削除
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');  // ボタンのアクティブ状態を解除
                    selectedFormats = selectedFormats.filter(id => id !== formatId);  // 配列からこのフォーマットIDを削除
                } else {
                    // そうでない場合は、アクティブ状態にしてフォーマットを追加
                    btn.classList.add('active');  // ボタンをアクティブにする
                    selectedFormats.push(formatId);  // 選択されたフォーマットIDを配列に追加
                }

                // 選択されたフォーマットIDをカンマで結合して、隠しフィールドの値を更新
                selectedFormatsInput.value = selectedFormats.join(',');
            });
        });
    });
</script>

<style>
    .format-btn {
        padding: 10px 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f3f4f6;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .format-btn.active {
        background-color: #4f46e5;
        color: white;
    }

</style>
