<x-app-layout>
    <div class="flex">
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
                <h2 class="text-xl font-bold">フレンドリスト</h2>
                <ul>
                    @if ($acceptedFriends && count($acceptedFriends) > 0)
                        @foreach($acceptedFriends as $friend)
                            <li>
                                <a href="{{ url('user/show', $friend->id) }}" class="text-blue-500 hover:underline">
                                    {{ $friend->name }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li>フレンドがいません</li>
                    @endif
                </ul>
            </div>

            <!-- フレンド申請中 -->
            <div id="pendingRequests" class="content-section hidden">
                <h2 class="text-xl font-bold">フレンド申請中</h2>
                <ul>
                    @if ($pendingFriends && $pendingFriends->isNotEmpty())
                        @foreach($pendingFriends as $pending)
                            <li>
                                <a href="{{ route('user.show', $pending->friend->id) }}" class="text-blue-500 hover:underline">
                                    {{ $pending->friend->name }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li>申請中のフレンドはありません</li>
                    @endif
                </ul>
            </div>

            <!-- 承認待機中 -->
            <div id="approvalWaiting" class="content-section hidden">
                <h2 class="text-xl font-bold">承認待機中</h2>
                <ul>
                    @if ($approvalWaiting && $approvalWaiting->isNotEmpty())
                        @foreach($approvalWaiting as $waiting)
                            <li>
                                <a href="{{ route('user.show', $waiting->user->id) }}" class="text-blue-500 hover:underline">
                                    {{ $waiting->user->name }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li>承認待機中のフレンドはありません</li>
                    @endif
                </ul>
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

<!-- Tailwind CSSのhiddenクラスを利用 -->
<style>
.hidden {
    display: none;
}

.menu-button {
    @apply block text-center text-white py-2 px-4 rounded bg-gray-400;
}
</style>
