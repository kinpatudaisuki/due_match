
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('トークルーム一覧') }}
        </h2>
    </x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if($rooms->isEmpty())
            <p>参加中のルームはありません。</p>
        @else
            <ul>
                @foreach($rooms as $room)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                        <a href="{{ route('room.show', $room->id) }}">
                            メンバー：
                            <div class="flex items-center space-x-4">
                                @foreach($room->users as $user)
                                    <div class="flex items-center">
                                        @if($user->image)
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                        @else
                                            <span class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-700">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </span>
                                        @endif
                                        <span class="ml-2">{{ $user->name }}</span>
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
