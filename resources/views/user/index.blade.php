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
                @if ( $user->id != auth()->user()->id )
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                    <form id="create-room-form">
                            <div class="p-6 text-gray-900">
                                <p>{{$user->name}}</p>
                                <p>エリア：{{$user->area}}</p>
                                <p id="select_user" user_id={{$user->id}}></p>
                            </div>
                        <button type="submit">トークを開始する</button>
                    </form>
                </div>
                @endif
            @endforeach
        @endif
      </div>
  </div>
</x-app-layout>

<script>
    document.getElementById('create-room-form').addEventListener('submit', function(event) {

        // フォームのデフォルト動作をキャンセル
        event.preventDefault();

        // 選択したユーザーのIDを取得
        const selectUser = document.getElementById('select_user');
        const selectUserId = selectUser.getAttribute('user_id');

        // ログインしている自分のIDを取得
        const myId = {{ auth()->user()->id }};

        // サーバーにルーム作成、もしくは既存ルーム確認のリクエストを送る
        axios.post('/room/store', {
            user_ids: [myId, selectUserId]
        })
        .then(function(response) {
            // サーバーから返ってきたルームIDに基づいてリダイレクト
            const roomId = response.data.room_id;
            window.location.href = `/room/index/${roomId}`;
        })
        .catch(function(error) {
            // エラーが発生した場合の処理
            console.error(error);
            alert('ルーム作成に失敗しました');
        });
    });
</script>
