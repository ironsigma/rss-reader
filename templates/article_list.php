<!doctype html>
<html lang="en">
<head>
    <title>RSS Reader</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/static/css/article.css">
</head>
<body>
    <h1><?php echo $feed->name ?> (<?php echo $article_count ?>)</h1>
    <div>
    <?php if ($page > 1) : ?>
        <a href="/feed/<?php echo $feed->id ?>/articles?page=<?php echo $page - 1 ?>">&laquo; Prev</a>
    <?php endif ?>
    Page <?php echo $page ?> of <?php echo $page_count ?>
    <?php if ($page < $page_count) : ?>
        <a href="/feed/<?php echo $feed->id ?>/articles?page=<?php echo $page + 1 ?>">Next &raquo;</a>
    <?php endif ?>
    <a style="margin-left:20px" href="">&times; Mark Page Read</a>
    </div>
    <?php foreach($articles as $a) : ?>
    <div id="id_<?php echo $a->id ?>" class='article'>
        <div class='star'><img src="/static/images/star_<?php echo $a->stared?'full':'empty' ?>.png"/></div>
        <h2><a href="<?php echo $a->link ?>"><?php echo $a->title ?></a></h2>
        <h3><?php echo $a->id ?>: <?php echo date('D M j, Y &\m\d\a\s\h; g:i a', $a->ts) ?></h3>
        <div class='text'><?php echo $a->text ?></div>
    </div>
    <?php endforeach ?>
</body>
</html>
