<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="antialiased">
        <div class="relative flex flex-col justify-center items-center min-h-screen bg-center bg-gray-200 font-semibold text-xl">
            <x-application-logo width="35" class="pb-10"/>
            <p>
                GrayZoneへようこそ！
            </p>
            </p>
                ここは発達障害のグレーゾーンの方や、
            <p>
            </p>
                自分もグレーゾーンかもしれないと思っている方が、
            <p>
            </p>
                悩みや愚痴を投稿する掲示板です。
            <p>
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('post/index') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-blue-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">投稿一覧</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-blue-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">ログイン</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-blue-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">会員登録</a>
                        @endif
                    @endauth
                </div>
            @endif
            <div class="font-semibold text-blue-400 hover:text-gray-900 dark:text-brue-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500 mt-20">
                <a href="{{route('contact.create')}}">お問い合わせ</a>
            </div>
        </div>
    </body>
</html>
