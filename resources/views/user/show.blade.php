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
                    {{-- ブロックされている場合 --}}
                    @if ($isBlocked)
                        <p class="text-red-500 font-bold">ブロックされています</p>
                    @else
                        {{-- ユーザーの画像を表示 --}}
                        @if ($user_data->image)
                            @if (app()->environment('production'))
                                {{-- Production環境ではS3から画像を取得 --}}
                                <img src="{{ Storage::disk('s3')->url($user_data->image) }}" alt="{{ $user_data->name }}" class="w-24 h-24 rounded-full object-cover">
                            @else
                                {{-- Production以外ではローカルストレージから画像を取得 --}}
                                <img src="{{ asset('storage/' . $user_data->image) }}" alt="{{ $user_data->name }}" class="w-24 h-24 rounded-full object-cover">
                            @endif
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
                            <p class="text-gray-500">
                                エリア：
                                <span class="bg-green-500 text-white px-3 py-1 rounded-md">
                                    {{ $user_data->area }}
                                </span>
                            </p>

                            {{-- フォーマットの表示 --}}
                            <div class="text-gray-500">
                                フォーマット：
                                <div class="grid grid-cols-2 gap-1 mt-1">
                                    @if ($user_data->formats->isNotEmpty())
                                        @foreach ($user_data->formats as $format)
                                            <span class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm">
                                                {{ $format->name }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span>なし</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4">
                                <p class="text-gray-700">
                                    {!! implode('<br>', mb_str_split($user_data->introduction ?? '', 20, 'UTF-8')) !!}
                                </p>
                            </div>
                            

                            <p class="text-gray-500">合計評価数：{{ $user_data->total_rate ?? 0 }}</p>
                        </div>

                        @if (Auth::check())
                            {{-- 評価機能の表示 --}}
                            <div class="rating-section mt-4">
                                @if ($isRated)
                                    {{-- 評価済みの場合 --}}
                                    <p class="text-green-500 font-bold">評価済みです</p>
                                @else
                                    {{-- 未評価の場合 --}}
                                    <div class="flex items-center space-x-2">
                                        <p class="text-gray-500">このユーザーを評価する:</p>
                                        <div class="flex space-x-2">
                                            @for ($i = 1; $i <= 3; $i++)
                                                <button 
                                                    class="star-btn text-gray-500 hover:text-yellow-500" 
                                                    id="star-{{ $i }}" 
                                                    data-rating="{{ $i }}" 
                                                    onclick="submitRating({{ $user_data->id }}, {{ $i }})"
                                                    onmouseover="highlightStars({{ $i }})"
                                                    onmouseout="resetStars()"
                                                >
                                                    ★
                                                </button>
                                            @endfor
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- ゲストユーザーはトーク開始ボタン非表示 --}}
                            @auth
                                {{-- ブロック状態に応じたメッセージまたはボタンを表示 --}}
                                @if ($hasBlocked)
                                    <div class="flex justify-center mt-2">
                                        <p class="text-red-500">ブロックしています</p>
                                    </div>
                                @else
                                    <div class="flex justify-center mt-2">
                                        <button type="button" 
                                                class="bg-blue-500 text-white py-2 px-4 rounded"
                                                data-user-id="{{ $user_data->id }}" 
                                                data-user-name="{{ $user_data->name }}" 
                                                onclick="startChat(this)">
                                            トークを開始する
                                        </button>
                                    </div>
                                @endif
                            @endauth

                            {{-- フレンド申請ボタン --}}
                            @if (!$hasBlocked && !$isBlocked) 
                                <div class="mt-6">
                                    @if ($isFriendRequestPending)
                                        <button class="bg-gray-400 text-white px-4 py-2 rounded-md" disabled>
                                            フレンド申請済み
                                        </button>
                                        @elseif ($isReceivedFriendRequest)
                                        {{-- フレンド申請を受け取った場合 --}}
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <button class="bg-blue-500 text-white px-4 py-2 rounded-md w-full sm:w-auto"
                                                    onclick="approveFriendRequest({{ $user_data->id }})">
                                                フレンド申請を承認
                                            </button>
                                            <button class="bg-red-500 text-white px-4 py-2 rounded-md w-full sm:w-auto"
                                                    onclick="denyFriendRequest({{ $user_data->id }})">
                                                フレンド申請を拒否
                                            </button>
                                        </div>
                                    @elseif ($isFriend) 
                                        {{-- フレンド関係なら解除ボタンを表示 --}}
                                        <button class="bg-red-600 text-white px-4 py-2 rounded-md" onclick="removeFriend({{ $user_data->id }})">
                                            フレンド解除
                                        </button>
                                    @else
                                        {{-- フレンド申請が送られていない場合 --}}
                                        <button class="bg-green-500 text-white px-4 py-2 rounded-md" onclick="sendFriendRequest({{ $user_data->id }}, this)">
                                            フレンド申請を送る
                                        </button>
                                    @endif
                                </div>
                            @endif

                            {{-- ブロック機能の表示 --}}
                            <div class="block-section mt-6">
                                @if ($hasBlocked)
                                    <button class="bg-red-500 text-white px-4 py-2 rounded-md" onclick="unblockUser({{ $user_data->id }})">
                                        ブロックを解除
                                    </button>
                                @else
                                    <button class="bg-red-500 text-white px-4 py-2 rounded-md" onclick="blockUser({{ $user_data->id }})">
                                        このユーザーをブロック
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif

                    <div class="mx-auto">
                        <a href="javascript:history.back()" class="block w-48 mx-auto text-center bg-blue-500 text-white py-2 rounded-md">
                            戻る
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    let currentRating = 0;  // 現在の評価

    function submitRating(userId, rating) {
        if (confirm(`評価を${rating}にしますか？\n一度した評価は変更できません`)) {
            axios.post('/rate', {
                rated_id: userId,
                rating: rating
            })
            .then(function (response) {
                alert(response.data.message);
                currentRating = rating;  // 評価を保存
                updateStarRating(rating);  // 確定した評価に基づいて星を更新
                location.reload();  // ページをリロードして評価済み表示に変更
            })
            .catch(function (error) {
                console.error(error);
                alert('評価に失敗しました');
            });
        }
    }

    function updateStarRating(rating) {
        // 全ての星をリセット
        document.querySelectorAll('.star-btn').forEach(function(star) {
            star.classList.remove('text-yellow-500');  // 色をリセット
            star.classList.add('text-gray-500');       // 元の色に戻す
        });

        // 評価に応じて星の色を変更
        for (let i = 1; i <= rating; i++) {
            document.getElementById('star-' + i).classList.remove('text-gray-500');
            document.getElementById('star-' + i).classList.add('text-yellow-500');
        }
    }

    function highlightStars(rating) {
        // マウスが触れているときに星の色を変更
        for (let i = 1; i <= rating; i++) {
            document.getElementById('star-' + i).classList.remove('text-gray-500');
            document.getElementById('star-' + i).classList.add('text-yellow-500');
        }
    }

    function resetStars() {
        // マウスが外れたら現在の評価に応じて星の色を元に戻す
        updateStarRating(currentRating);
    }

    function startChat(button) {
        // フォームのデフォルト送信を防ぐ
        event.preventDefault();

        // ボタンのデータ属性からユーザーIDと名前を取得
        const selectUserId = button.getAttribute('data-user-id');
        const selectUserName = button.getAttribute('data-user-name');

        let myId = null;

        // 確認ポップアップを表示し、OKが押された場合のみ処理を続行
        if (confirm(`${selectUserName}とトークを開始しますか？`)) {
            // 自分のユーザーIDを取得
            @if(auth()->check())
                myId = {{ auth()->user()->id }};
            @endif

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

    function sendFriendRequest(userId, button) {
        axios.post(`/friend/request/${userId}`, {
            user_id: userId
        }, {
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.data.success) {
                button.textContent = "申請済み";
                button.disabled = true;
                button.classList.remove("bg-green-500");
                button.classList.add("bg-gray-400");
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
    }

    function approveFriendRequest(userId) {
        axios.post(`/friend/approve/${userId}`, {}, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (response.data.success) {
                alert("フレンド申請を承認しました！");
                location.reload(); // ページをリロードして最新の状態に更新
            } else {
                alert("承認に失敗しました。");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("エラーが発生しました。");
        });
    }

    function denyFriendRequest(userId) {
        if (!confirm('このフレンド申請を拒否しますか？')) {
            return;
        }

        axios.delete(`/friend/deny/${userId}`)
            .then(response => {
                if (response.data.success) {
                    alert(response.data.message);
                    location.reload(); // ページをリロードしてボタンを更新
                } else {
                    alert(response.data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("エラーが発生しました。");
            });
    }

    function removeFriend(userId) {
        if (!confirm('本当にフレンドを解除しますか？')) {
            return;
        }

        axios.delete(`/friend/remove/${userId}`)
            .then(response => {
                if (response.data.success) {
                    alert(response.data.message);
                    location.reload(); // ページをリロードしてボタンを更新
                } else {
                    alert(response.data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("エラーが発生しました。");
            });
    }

    // ユーザーをブロック
    function blockUser(userId) {
        if (confirm('このユーザーをブロックしますか？')) {
            axios.post('/block/' + userId)
                .then(function (response) {
                    alert(response.data.message);

                    // フレンド申請またはフレンド関係を削除
                    axios.post('/block/remove-friendship/' + userId)
                        .then(function (response) {
                            console.log('フレンド申請・関係を削除:', response.data);
                        })
                        .catch(function (error) {
                            console.error('フレンドデータの削除に失敗:', error);
                        });

                    location.reload();  // ページをリロードしてブロック表示を更新
                })
                .catch(function (error) {
                    console.error(error);
                    alert('ブロックに失敗しました');
                });
        }
    }

    // ブロックを解除
    function unblockUser(userId) {
        if (confirm('このユーザーのブロックを解除しますか？')) {
            axios.post('/unblock/' + userId)
                .then(function (response) {
                    alert(response.data.message);
                    location.reload();  // ページをリロードしてブロック解除表示を更新
                })
                .catch(function (error) {
                    console.error(error);
                    alert('ブロック解除に失敗しました');
                });
        }
    }
</script>
