
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
                        メンバー：{{ $room->users->pluck('name')->implode(', ') }}
                    </div>
                @endforeach
            </ul>
        @endif
    </div>
</div>
</x-app-layout>
