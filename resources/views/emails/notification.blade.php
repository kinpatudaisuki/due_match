<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>
<body>
    <p>デュエマッチよりお知らせです。</p>
    <p>{{ $senderName }}さんから新しいメッセージがあります:</p>
    @if(empty($messageBody))
        <p>新しい画像を投稿しました。</p>
    @else
        <p>{{ $messageBody }}</p>
    @endif
    <p><a href="https://due-match.com">こちらをクリックしてデュエマッチに遷移できます。</a></p>
</body>
</html>