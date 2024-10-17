<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url('/img/background.png');
            background-size: 600px 600px;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>

<body class="antialiased">
    <div class="relative flex flex-col justify-between min-h-screen font-semibold text-xl">
        <div class="flex-grow flex flex-col justify-end items-center mb-4 text-white">

            <p>
                ここではデュエル・マスターズの
            </p>
            <p>
                チーム戦に一緒に出るメンバーや、対戦相手とマッチングできます
            </p>
        </div>

        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('user/index') }}" class="ml-4 font-semibold text-blue-300">ユーザー一覧</a>
                @else
                    <a href="{{ route('login') }}" class="ml-4 font-semibold text-blue-300">ログイン</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-blue-300">会員登録</a>
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
