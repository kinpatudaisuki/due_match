<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('トーク') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- 参加しているユーザー一覧 -->
            <div class="bg-white shadow-sm sm:rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-lg mb-2">参加ユーザー一覧</h3>
                <div class="flex flex-wrap">
                    @foreach ($room_users as $user)
                        <div class="p-2 border rounded-md m-1 bg-gray-200">
                            <p>{{ $user->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ユーザー招待フォーム -->
            <form id="invite_user_form" class="mb-6">
                <h4 class="font-semibold text-lg mb-2">ユーザーを招待</h4>
                <!-- 招待したいユーザーのIDを選択 -->
                <select id="select_user" name="user_id" class="border-gray-300 rounded-md">
                    @foreach ($all_users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-md">招待</button>
            </form>

            <!-- トークルームのメッセージ表示部分 -->
            <div id="chat_window" class="bg-white overflow-auto shadow-sm sm:rounded-lg p-4" style="height: 400px; border: 1px solid #ccc;">
                @foreach ($messages as $message)
                    <div class="p-2 {{ $message->user_id == auth()->id() ? 'text-right' : 'text-left' }}">
                        <p><strong>{{ $message->user->name }}:</strong> {{ $message->body }}</p>
                    </div>
                @endforeach
            </div>

            <!-- メッセージ送信フォーム -->
            <form id="message_form" class="mt-4 flex items-center">
                <input type="text" id="message_input" name="message" class="border-gray-300 rounded-md" style="width: 900px;" placeholder="メッセージを入力" required>
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md">送信</button>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatWindow = document.getElementById('chat_window');
        
        // 最後のメッセージにスクロールする
        chatWindow.scrollTop = chatWindow.scrollHeight;

        // メッセージ送信イベント
        document.getElementById('message_form').addEventListener('submit', function(event) {
            event.preventDefault();

            const messageInput = document.getElementById('message_input');
            const message = messageInput.value;

            if (message.trim() === '') return;

            // 現在のURLからルームIDを取得
            const currentUrl = window.location.href;
            const roomId = currentUrl.split('/').pop();

            // サーバーにメッセージを送信
            axios.post(`/room/${roomId}/send_message`, {
                message: message
            })
            .then(function(response) {
                // メッセージ送信後にチャットウィンドウを更新
                const newMessage = document.createElement('div');
                newMessage.className = 'p-2 text-right';
                newMessage.innerHTML = `<p><strong>{{ auth()->user()->name }}:</strong> ${message}</p>`;
                chatWindow.appendChild(newMessage);

                // 入力欄をクリアしてスクロールを下に移動
                messageInput.value = '';
                chatWindow.scrollTop = chatWindow.scrollHeight;
            })
            .catch(function(error) {
                console.error(error);
                alert('メッセージの送信に失敗しました');
            });
        });

        // ユーザー招待フォーム
        document.getElementById('invite_user_form').addEventListener('submit', function(event) {
            event.preventDefault();

            const selectUser = document.getElementById('select_user');
            const userId = selectUser.value;

            const currentUrl = window.location.href;
            const roomId = currentUrl.split('/').pop();

            // サーバーに招待リクエストを送信
            axios.post(`/room/${roomId}/invite`, {
                user_id: userId
            })
            .then(function(response) {
                alert(response.data.message);
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
    });
</script>
