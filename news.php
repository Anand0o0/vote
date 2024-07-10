<?php
//$apiKey = '57e27d383ddb429c9b8dde35d2314a7c';

function getNews($apiKey) {
    $apiUrl = "https://newsapi.org/v2/top-headlines?country=in&apiKey=57e27d383ddb429c9b8dde35d2314a7c";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $apiUrl);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

$newsData = getNews('57e27d383ddb429c9b8dde35d2314a7c');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Latest News</title>
</head>
<body>
    <h1>Latest News</h1>
    <?php if (isset($newsData['articles'])) : ?>
        <ul>
            <?php foreach ($newsData['articles'] as $article) : ?>
                <li>
                    <h2><?= htmlspecialchars($article['title']) ?></h2>
                    <p><?= htmlspecialchars($article['description']) ?></p>
                    <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank">Read more</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>No news available at the moment.</p>
    <?php endif; ?>
</body>
</html>