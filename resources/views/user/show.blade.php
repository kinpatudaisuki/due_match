<x-app-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
          {{ __('ユーザー詳細') }}
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
              <div class="flex flex-col items-center space-y-6">
                  {{-- ユーザーの画像を表示 --}}
                  @if ($user_data->image)
                      <img src="{{ asset('storage/' . $user_data->image) }}" alt="{{ $user_data->name }}" class="w-24 h-24 rounded-full object-cover">
                  @else
                      {{-- 画像がない場合にデフォルトの画像を表示 --}}
                      <div class="w-24 h-24 rounded-full bg-gray-300 flex items-center justify-center">
                          <span class="text-2xl text-gray-700">{{ strtoupper(substr($user_data->name, 0, 1)) }}</span>
                      </div>
                  @endif

                  <div class="text-center">
                      {{-- ユーザーの名前 --}}
                      <h3 class="text-2xl font-bold">{{ $user_data->name }}</h3>

                      {{-- ユーザーのエリア --}}
                      <p class="text-gray-500">エリア：{{ $user_data->area }}</p>

                      {{-- フォーマットの表示 --}}
                      <p class="text-gray-500">
                          フォーマット：
                          @if ($user_data->formats->isNotEmpty())
                              {{ implode(', ', $user_data->formats->pluck('name')->toArray()) }}
                          @else
                              なし
                          @endif
                      </p>
                  </div>

                  <div class="flex justify-center mt-6 space-x-4">
                      {{-- トークを開始するボタン --}}
                      <form id="create-room-form">
                          <button type="submit" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded" onclick="startChat({{ $user_data->id }})">
                              トークを開始する
                          </button>

                  </div>
              </div>
          </div>
      </div>
  </div>
</x-app-layout>

<script>
  function startChat(userId) {
      // フォームのデフォルト送信を防ぐ
      event.preventDefault();

      if (confirm("トークを開始しますか？")) {
          // 自分のユーザーIDを取得
          const myId = {{ auth()->user()->id }};

          // Axiosでサーバーにルーム作成リクエストを送信
          axios.post('/room/store', {
              user_ids: [myId, userId]
          })
          .then(function(response) {
              if (response.data.message) {
                  alert(response.data.message);
              }
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
