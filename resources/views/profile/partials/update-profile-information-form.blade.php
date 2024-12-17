<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- 名前 -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- エリア -->
        <div>
            <x-input-label for="area" :value="__('都道府県')" />
            <select id="area" name="area" class="mt-1 block w-full" required>
                <option value="" disabled>都道府県を選択してください</option>
                @foreach (['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'] as $prefecture)
                    <option value="{{ $prefecture }}" {{ old('area', $user->area) == $prefecture ? 'selected' : '' }}>
                        {{ $prefecture }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('area')" />
        </div>


        <!-- プロフィール画像 -->
        <div>
            <x-input-label for="image" :value="__('プロフィール画像')" />
            <input id="image" name="image" type="file" class="mt-1 block w-full" accept="image/*" />
            <x-input-error class="mt-2" :messages="$errors->get('image')" />

            @if ($user->image)
                <div class="mt-2">
                    @if (app()->environment('production'))
                        {{-- Production環境ではS3から画像を取得 --}}
                        <img src="{{ Storage::disk('s3')->url($user->image) }}" alt="プロフィール画像" class="w-20 h-20 rounded-full object-cover">
                    @else
                        {{-- Production以外ではローカルストレージから画像を取得 --}}
                        <img src="{{ asset('storage/' . $user->image) }}" alt="プロフィール画像" class="w-20 h-20 rounded-full object-cover">
                    @endif
                </div>
            @endif
        </div>

        <!-- フォーマットボタン -->
        <div>
            <x-input-label for="formats" :value="__('フォーマットを選択')" />
            <div class="mt-2 flex flex-wrap gap-2 sm:gap-4">
                <button type="button" class="format-btn" data-format-id="1">オリジナル</button>
                <button type="button" class="format-btn" data-format-id="2">アドバンス</button>
                <button type="button" class="format-btn" data-format-id="3">2ブロック</button>
                <button type="button" class="format-btn" data-format-id="4">デュエパ</button>
                <button type="button" class="format-btn" data-format-id="5">殿堂ゼロ</button>
                <button type="button" class="format-btn" data-format-id="6">シールド戦</button>
            </div>

            <!-- 隠しフィールド -->
            <input type="hidden" name="formats" id="selected-formats" value="{{ old('formats', implode(',', $user->formats->pluck('id')->toArray())) }}">

            <x-input-error class="mt-2" :messages="$errors->get('formats')" />
        </div>

        <!-- 自己紹介 -->
        <div>
            <x-input-label for="introduction" :value="__('自己紹介')" />
            <textarea id="introduction" name="introduction" class="mt-1 block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" rows="4" autocomplete="introduction" placeholder="実績、休みの日、よく行くショップ、所持デッキなど300文字まで" maxlength="300">{{ old('introduction', $user->introduction) }}</textarea>
            <div id="charCount" class="text-sm text-gray-500 mt-1">残り <span id="remainingChars">300</span> 文字</div>
            <div id="warningMessage" class="text-sm text-red-500 mt-1 hidden">文字数が300を超えています。</div>
            <x-input-error class="mt-2" :messages="$errors->get('introduction')" />
        </div>

        <!-- 保存ボタン -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectedFormatsInput = document.getElementById('selected-formats');
        let selectedFormats = selectedFormatsInput.value ? selectedFormatsInput.value.split(',') : [];

        document.querySelectorAll('.format-btn').forEach(function (btn) {
            const formatId = btn.getAttribute('data-format-id');
            
            // 初期状態を設定
            if (selectedFormats.includes(formatId)) {
                btn.classList.add('active');
            }

            btn.addEventListener('click', function () {
                if (btn.classList.contains('active')) {
                    btn.classList.remove('active');
                    selectedFormats = selectedFormats.filter(id => id !== formatId);
                } else {
                    btn.classList.add('active');
                    selectedFormats.push(formatId);
                }
                
                // 隠しフィールドに選択されたフォーマットを更新
                selectedFormatsInput.value = selectedFormats.join(',');
            });
        });

        const textarea = document.getElementById('introduction');
        const remainingChars = document.getElementById('remainingChars');
        const warningMessage = document.getElementById('warningMessage');

        textarea.addEventListener('input', function() {
            const currentLength = textarea.value.length;
            const maxLength = 300;

            // 残り文字数の更新
            remainingChars.textContent = maxLength - currentLength;

            // 文字数が300を超える場合に警告メッセージを表示
            if (currentLength > maxLength) {
                warningMessage.classList.remove('hidden');
            } else {
                warningMessage.classList.add('hidden');
            }
        });
    });
</script>

<style>
    .format-btn {
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f3f4f6;
        cursor: pointer;
        transition: background-color 0.3s;
        flex: 1 1 calc(50% - 10px); /* スマホ画面ではボタン幅を50%に調整 */
        text-align: center;
        box-sizing: border-box;
    }

    .format-btn.active {
        background-color: #4f46e5;
        color: white;
    }

    /* 大きい画面ではボタンのサイズを調整 */
    @media (min-width: 640px) {
        .format-btn {
            flex: none;
            width: auto;
        }
    }

</style>
