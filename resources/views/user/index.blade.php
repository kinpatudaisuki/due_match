<!-- Axiosを使用 -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('ユーザー一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($users)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 justify-items-center">
                    @foreach ($users as $user)
                        {{-- 自分のIDと異なるユーザーを表示 --}}
                        @if ($user->id != auth()->user()->id)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4 p-4 w-48 flex flex-col items-center">
                                <form id="create-room-form">
                                    <div class="flex flex-col items-center space-y-4">
                                        {{-- ユーザーの画像を表示 --}}
                                        @if ($user->image)
                                            <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                                        @else
                                            {{-- 画像がない場合にデフォルトの画像を表示 --}}
                                            <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-700">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                        <div class="text-center">
                                            <p class="font-semibold">{{ $user->name }}</p>
                                            <p class="text-gray-500">エリア：{{ $user->area }}</p>

                                            {{-- フォーマットを表示 --}}
                                            <p class="text-gray-500">
                                                フォーマット：
                                                @if ($user->formats->isNotEmpty())
                                                    {{ implode(', ', $user->formats->pluck('name')->toArray()) }}
                                                @else
                                                    なし
                                                @endif
                                            </p>

                                            <p class="text-gray-500">合計評価数：{{ $user->total_rate ?? 0 }}</p>

                                            <p id="select_user_{{ $user->id }}" user_id="{{ $user->id }}"></p>
                                        </div>
                                    </div>
                                    <div class="flex justify-center mt-2">
                                        <button type="submit" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded" onclick="startChat({{ $user->id }})">
                                            トークを開始する
                                        </button>
                                    </div>
                                    <div class="flex justify-center mt-2">
                                        <a href="{{ route('user.show', $user->id) }}" class="text-sm bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded">
                                            ユーザー詳細
                                        </a>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    function startChat(userId) {
        // フォームのデフォルト送信を防ぐ
        event.preventDefault();

        // 確認ポップアップを表示し、OKが押された場合のみ処理を続行
        if (confirm("トークを開始しますか？")) {
            // 選択したユーザーのIDを取得
            const selectUser = document.getElementById('select_user_' + userId);
            const selectUserId = selectUser.getAttribute('user_id');

            // 自分のユーザーIDを取得
            const myId = {{ auth()->user()->id }};

            // Axiosでサーバーにルーム作成リクエストを送信
            axios.post('/room/store', {
                user_ids: [myId, selectUserId]
            })
            .then(function(response) {
                // レスポンスにmessageが含まれていればアラートで表示
                if (response.data.message) {
                    alert(response.data.message);
                }

                // ルームIDに基づいてリダイレクト
                const roomId = response.data.room_id;
                window.location.href = `/room/show/${roomId}`;
            })
            .catch(function(error) {
                console.error(error);
                alert('ルーム作成に失敗しました');
            });
        }
    }
</script>
