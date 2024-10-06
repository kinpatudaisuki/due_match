<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('トーク') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @foreach ($room_users as $user)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
            <div class="p-6 text-gray-900">
                <p>{{ $user->name }}</p>
                <p>エリア：{{ $user->area }}</p>
            </div>
        </div>
        @endforeach
        </div>
    </div>

    <form id="invite-user-form">
        <!-- 招待したいユーザーのIDを選択 -->
        <select id="select_user" name="user_id">
            @foreach ($all_users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    
        <button type="submit">ユーザーを招待</button>
    </form>

</x-app-layout>

<script>
    document.getElementById('invite-user-form').addEventListener('submit', function(event) {
        // フォームのデフォルト送信を防ぐ
        event.preventDefault();

        // 現在のURLからルームIDを取得
        const currentUrl = window.location.href;
        const roomId = currentUrl.split('/').pop();

        // 招待するユーザーIDを取得
        const selectUser = document.getElementById('select_user');
        const userId = selectUser.value;

        // サーバーに招待リクエストを送信
        axios.post(`/room/${roomId}/invite`, {
            user_id: userId
        })
        .then(function(response) {
            alert(response.data.message);
            // ユーザーを招待した後、ページを再読み込み
            location.reload();
        })
        .catch(function(error) {
            console.error(error);
            if (error.response) {
                alert('ユーザーの招待に失敗しました: ' + error.response.data.message);
            } else {
                alert('ユーザーの招待に失敗しました: ' + error.message);
            }
        });

    });
</script>