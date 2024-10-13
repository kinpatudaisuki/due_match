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
                        <div class="p-2 m-1 flex items-center">
                            <!-- ユーザーの画像を表示 -->
                            @if($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                            @else
                                <span class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-700">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            @endif
                            <p>{{ $user->name }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- ユーザー招待フォーム -->
            <form id="invite_user_form" class="mb-6">
                <h4 class="font-semibold text-lg mb-2">ユーザーを招待</h4>
                <!-- 検索ボックス -->
                <input type="text" id="search_user" placeholder="ユーザー名で検索" class="border-gray-300 rounded-md mb-2 w-full">

                <button type="submit" class="ml-2 px-4 py-2 bg-green-500 text-white rounded-md mt-2">招待</button>
            </form>

            <!-- トークルームのメッセージ表示部分 -->
            <div id="chat_window" class="bg-white overflow-auto shadow-sm sm:rounded-lg p-4" style="height: 400px; border: 1px solid #ccc;">
                @foreach ($messages as $message)
                    <div class="p-2 {{ $message->user_id == auth()->id() ? 'text-right flex justify-end' : 'text-left flex' }}">
                        @if($message->user_id)
                            <!-- ユーザーの画像またはイニシャルを表示 -->
                            @if($message->user && $message->user->image)
                                <img src="{{ asset('storage/' . $message->user->image) }}" alt="{{ $message->user->name }}" class="w-10 h-10 rounded-full mr-2">
                            @else
                                <span class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 mr-2">
                                    {{ $message->user ? strtoupper(substr($message->user->name, 0, 1)) : '?' }}
                                </span>
                            @endif
                            <p><strong>{{ $message->user->name ?? 'Unknown User' }}:</strong> {{ $message->body }}</p>
                        @else
                            <!-- user_idがないメッセージは中央揃えで表示 -->
                            <div class="text-center w-full">
                                <p>{{ $message->body }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- メッセージ送信フォーム -->
            <form id="message_form" class="mt-4 flex items-center">
                <input type="text" id="message_input" name="message" class="border-gray-300 rounded-md" style="width: 900px;" placeholder="メッセージを入力" required>
                <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md">送信</button>
            </form>

            <!-- トークルームから退会するボタン -->
            <form action="{{ route('room.leave', $room_id) }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md">トークルームから退会する</button>
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
                newMessage.className = 'p-2 text-right flex justify-end';
                newMessage.innerHTML = `<span class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-700 mr-2">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span><p><strong>{{ auth()->user()->name }}:</strong> ${message}</p>`;
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

        // ユーザー検索イベント
        const searchInput = document.getElementById('search_user');
        var matchedUser = null;

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value.toLowerCase();
            matchedUser = @json($all_users).find(user => user.name.toLowerCase().includes(searchTerm));

            if (matchedUser) {
                searchInput.classList.remove('border-red-500');
            } else {
                searchInput.classList.add('border-red-500');
            }
        });

        // ユーザー招待フォーム
        document.getElementById('invite_user_form').addEventListener('submit', function(event) {
            event.preventDefault();

            if (!matchedUser) {
                alert('有効なユーザーを選択してください');
                return;
            }

            const currentUrl = window.location.href;
            const roomId = currentUrl.split('/').pop();

            // サーバーに招待リクエストを送信
            axios.post(`/room/${roomId}/invite`, {
                user_id: matchedUser.id
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
