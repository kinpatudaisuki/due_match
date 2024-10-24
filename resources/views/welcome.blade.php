<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="antialiased bg-blue-400">
    <div class="relative flex flex-col justify-between min-h-screen text-xl">
        <div class="flex-grow flex flex-col justify-end items-center text-white">
            <img src="{{asset('img/logo.png')}}" class="w-48 mb-8">
            <div class="mb-12 text-center">
                <p>
                    デュエマッチへようこそ！
                </p>
                <p>
                    ここではデュエル・マスターズの
                </p>
                <p>
                    チーム戦に一緒に出るメンバーや
                </p>
                <p>
                    対戦相手とマッチングできます
                </p>
            </div>
        </div>

        @if (Route::has('login'))
            <div class="absolute top-0 right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('user/index') }}" class="ml-4 font-semibold text-white">ユーザー一覧</a>
                @else
                    <a href="{{ route('login') }}" class="ml-4 font-semibold text-white">ログイン</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-white">会員登録</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="font-semibold text-white text-center">
            <a href="{{ route('contact.create') }}">お問い合わせ</a>
        </div>
    </div>
</body>
</html>
