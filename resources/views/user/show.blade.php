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

                        <p class="text-gray-500">合計評価数：{{ $user_data->total_rate ?? 0 }}</p>
                    </div>

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
                location.reload();  // ページをリロードして評価済み表示に変更（オプション）
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

    // ユーザーをブロック
    function blockUser(userId) {
        if (confirm('このユーザーをブロックしますか？')) {
            axios.post('/block/' + userId)
                .then(function (response) {
                    alert(response.data.message);
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
