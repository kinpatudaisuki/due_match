<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight text-center">
            {{ __('フレンド') }}
        </h2>
    </x-slot>

    <div class="py-8 flex">
        <!-- 左側のメニュー -->
        <div class="w-1/4 bg-gray-100 p-4 min-h-screen">
            <nav class="flex flex-col space-y-4">
                <button id="btn-friendList" onclick="showSection('friendList', this)" class="menu-button">
                    フレンドリスト
                </button>
                <button id="btn-pendingRequests" onclick="showSection('pendingRequests', this)" class="menu-button">
                    フレンド申請中
                </button>
                <button id="btn-approvalWaiting" onclick="showSection('approvalWaiting', this)" class="menu-button">
                    承認待機中
                </button>
            </nav>
        </div>

        <!-- 右側のコンテンツ -->
        <div class="w-3/4 p-4">
            <!-- フレンドリスト -->
            <div id="friendList" class="content-section">
                <h2 class="text-xl font-bold mb-4">フレンドリスト</h2>
                <div class="space-y-4">
                    @if ($acceptedFriends && count($acceptedFriends) > 0)
                        @foreach($acceptedFriends as $friend)
                            <a href="{{ url('user/show', $friend->id) }}" class="block bg-white shadow-md rounded-lg p-4 flex items-center hover:bg-gray-100 transition">
                                @if ($friend->image)
                                    @if (app()->environment('production'))
                                        <img src="{{ Storage::disk('s3')->url($friend->image) }}" alt="{{ $friend->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <img src="{{ asset('storage/' . $friend->image) }}" alt="{{ $friend->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @endif
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm text-gray-700">{{ strtoupper(substr($friend->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold">{{ $friend->name }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">
                                        {{ $friend->introduction ?? '' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="text-center text-gray-500">フレンドがいません</p>
                    @endif
                </div>
            </div>

            <!-- フレンド申請中 -->
            <div id="pendingRequests" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">フレンド申請中</h2>
                <div class="space-y-4">
                    @if ($pendingFriends && $pendingFriends->isNotEmpty())
                        @foreach($pendingFriends as $pending)
                            <a href="{{ route('user.show', $pending->friend->id) }}" class="block bg-white shadow-md rounded-lg p-4 flex items-center hover:bg-gray-100 transition">
                                @if ($pending->friend->image)
                                    @if (app()->environment('production'))
                                        <img src="{{ Storage::disk('s3')->url($pending->friend->image) }}" alt="{{ $pending->friend->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <img src="{{ asset('storage/' . $pending->friend->image) }}" alt="{{ $pending->friend->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @endif
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm text-gray-700">{{ strtoupper(substr($pending->friend->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold">{{ $pending->friend->name }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">
                                        {{ $pending->friend->introduction ?? '' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="text-center text-gray-500">申請中のフレンドはありません</p>
                    @endif
                </div>
            </div>

            <!-- 承認待機中 -->
            <div id="approvalWaiting" class="content-section hidden">
                <h2 class="text-xl font-bold mb-4">承認待機中</h2>
                <div class="space-y-4">
                    @if ($approvalWaiting && $approvalWaiting->isNotEmpty())
                        @foreach($approvalWaiting as $waiting)
                            <a href="{{ route('user.show', $waiting->user->id) }}" class="block bg-white shadow-md rounded-lg p-4 flex items-center hover:bg-gray-100 transition">
                                @if ($waiting->user->image)
                                    @if (app()->environment('production'))
                                        <img src="{{ Storage::disk('s3')->url($waiting->user->image) }}" alt="{{ $waiting->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <img src="{{ asset('storage/' . $waiting->user->image) }}" alt="{{ $waiting->user->name }}" class="w-12 h-12 rounded-full object-cover">
                                    @endif
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm text-gray-700">{{ strtoupper(substr($waiting->user->name, 0, 1)) }}</span>
                                    </div>
                                @endif
                                <div class="ml-4 flex-1">
                                    <h3 class="text-lg font-semibold">{{ $waiting->user->name }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">
                                        {{ $waiting->user->introduction ?? '' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <p class="text-center text-gray-500">承認待機中のフレンドはありません</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let savedSection = localStorage.getItem("selectedSection") || "friendList";
    let savedButton = document.getElementById(`btn-${savedSection}`);

    if (savedButton) {
        showSection(savedSection, savedButton);
    }
});

function showSection(sectionId, button) {
    // すべてのセクションを非表示にする
    document.querySelectorAll('.content-section').forEach(section => {
        section.classList.add('hidden');
    });

    // 選択したセクションを表示する
    document.getElementById(sectionId).classList.remove('hidden');

    // すべてのボタンの色をリセット
    document.querySelectorAll('.menu-button').forEach(btn => {
        btn.classList.remove('bg-blue-300');
        btn.classList.add('bg-gray-400');
    });

    // クリックされたボタンの色を水色に変更
    button.classList.remove('bg-gray-400');
    button.classList.add('bg-blue-300');

    // 選択したセクションをローカルストレージに保存
    localStorage.setItem("selectedSection", sectionId);
}
</script>
