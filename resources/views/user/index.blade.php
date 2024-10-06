<!-- Axiosを使用 -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ユーザー一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($users)
                @foreach ($users as $user)
                {{-- 自分のIDと異なるユーザーを表示 --}}
                    @if ($user->id != auth()->user()->id)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                            <form id="create-room-form">
                                <div class="p-6 text-gray-900">
                                    <p>{{ $user->name }}</p>
                                    <p>エリア：{{$user->area}}</p>
                                    <p id="select_user_{{ $user->id }}" user_id="{{ $user->id }}"></p>
                                </div>
                                <button type="submit" onclick="startChat({{ $user->id }})">トークを開始する</button>
                            </form>
                        </div>
                    @endif
                @endforeach
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