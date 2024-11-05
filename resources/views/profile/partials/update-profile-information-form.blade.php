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
            <div class="mt-2 flex gap-4">
                <button type="button" class="format-btn" data-format-id="1">オリジナル</button>
                <button type="button" class="format-btn" data-format-id="2">アドバンス</button>
                <button type="button" class="format-btn" data-format-id="3">その他</button>
            </div>

            <!-- 隠しフィールド -->
            <input type="hidden" name="formats" id="selected-formats" value="{{ old('formats', implode(',', $user->formats->pluck('id')->toArray())) }}">

            <x-input-error class="mt-2" :messages="$errors->get('formats')" />
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
