<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('トークルーム一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-8 lg:px-12">
            @if($rooms->isEmpty())
                <p>参加中のルームはありません。</p>
            @else
                <ul>
                    @foreach($rooms as $room)
                        <div class="bg-white overflow-hidden shadow-lg rounded-lg p-6 mb-6">
                            <a href="{{ route('room.show', $room->id) }}">
                                <p class="font-semibold mb-4">メンバー：</p>
                                <div class="flex flex-wrap items-center space-x-6">
                                    @foreach($room->users as $user)
                                        <div class="flex items-center space-x-3">
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
                                                <span class="w-12 h-12 flex items-center justify-center rounded-full bg-gray-200 text-gray-700">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <span class="ml-2 text-lg">{{ $user->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </a>
                        </div>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>
