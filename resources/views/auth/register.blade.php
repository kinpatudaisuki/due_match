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
            <x-input-label for="area" :value="__('都道府県(都府県不要)')" />
            <x-text-input id="area" class="block mt-1 w-full" type="text" name="area" :value="old('area')" required autocomplete="username" />
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
        const selectedFormatsInput = document.getElementById('selected-formats');
        let selectedFormats = selectedFormatsInput.value ? selectedFormatsInput.value.split(',') : [];

        document.querySelectorAll('.format-btn').forEach(function (btn) {
            const formatId = btn.getAttribute('data-format-id');
            
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

    .format-btn:hover {
        background-color: #e5e7eb; /* ホバー時の色 */
    }
</style>
