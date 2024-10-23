<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('ブロックしたユーザー一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($blockedUserData->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 justify-items-center">
                    @foreach ($blockedUserData as $user)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4 p-4 w-48 flex flex-col items-center">
                            <form id="create-room-form">
                                <div class="flex flex-col items-center space-y-4">
                                    {{-- ユーザーの画像を表示 --}}
                                    @if ($user->image)
                                        @if (app()->environment('production'))
                                            {{-- Production環境ではS3から画像を取得 --}}
                                            <img src="{{ Storage::disk('s3')->url($user->image) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            {{-- Production以外ではローカルストレージから画像を取得 --}}
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                                        @endif
                                    @else
                                        {{-- 画像がない場合にデフォルトの画像を表示 --}}
                                        <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <div class="text-center">
                                        <p class="font-semibold">{{ $user->name }}</p>
                                    </div>
                                </div>

                                <div class="flex justify-center mt-2">
                                    <a href="{{ route('user.show', $user->id) }}" class="text-sm bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded">
                                        ユーザー詳細
                                    </a>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500">ブロックしたユーザーがいません。</p>
            @endif
        </div>
    </div>
</x-app-layout>
