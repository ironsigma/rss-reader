<!doctype html>
<html lang="en">
<head>
    <title>RSS Reader</title>
    <meta charset="utf-8" />
</head>
<body>
    <h1>Reader v1.0</h1>
    <ul>
    <?php foreach ( $feeds as $feed ) : ?>
        <li><a href="/feed/<?php echo $feed->id ?>/articles?page=1"><?php echo $feed->name ?></a> (<?php echo $feed->unread ?>)</li>
    <?php endforeach ?>
    </ul>
</body>
</html>
