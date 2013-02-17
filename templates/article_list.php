<!doctype html>
<html lang="en">
<head>
    <title>RSS Reader</title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/static/css/article.css">
    <script type="text/javascript" src="/static/js/jquery.js"></script>
</head>
<?php
    // Generate pager
    ob_start();
?>
    <div>
    <?php if ($page > 1) : ?>
        <a href="/feed/<?php echo $feed->id ?>/articles?page=<?php echo $page - 1 ?>">&laquo; Prev</a>
    <?php endif ?>
    Page <?php echo $page ?> of <?php echo $page_count ?>
    <?php if ($page < $page_count) : ?>
        <a href="/feed/<?php echo $feed->id ?>/articles?page=<?php echo $page + 1 ?>">Next &raquo;</a>
    <?php endif ?>
    <?php if ($article_count > 0 ) : ?>
    <a style="margin-left:20px" href="/feed/<?php echo $feed->id ?>/read?page=<?php echo $page ?>&ids=<?php echo $article_ids ?>">&times; Mark Page Read</a>
    <?php endif ?>
    </div>
<?php
    // Capture pager
    $pager = ob_get_contents();
    ob_end_clean();
?>
<body>
    <a href="/">Back To Feed List</a>
    <h1><?php echo $feed->name ?> (<?php echo $article_count ?>)</h1>
    <?php if ( $article_count === 0 ) : ?>
    <p>No unread items.</p>
    <?php else : ?>
    <?php echo $pager ?>
    <?php foreach($articles as $a) : ?>
    <div class='article'>
        <div id="star_<?php echo $a->stared?'1':'0' ?>_<?php echo $a->id ?>" class='star'><img id="star_img_<?php echo $a->id ?>" src="/static/images/star_<?php echo $a->stared?'full':'empty' ?>.png"/></div>
        <h2><a href="<?php echo $a->link ?>"><?php echo $a->title ?></a></h2>
        <h3><?php echo $a->id ?>: <?php echo date('D M j, Y &\m\d\a\s\h; g:i a', $a->ts) ?></h3>
        <div class='text'><?php echo $a->text ?></div>
    </div>
    <?php endforeach ?>
    <?php echo $pager ?>
    <?php endif ?>
    <script type="text/javascript">
    $(document).ready(function(){
        $(".star").click(function(event) {
            var star = this;
            var star_value = this.id.substr(5, 1) != '1';
            var post_id = this.id.substr(7);
            $.ajax({
                url: '/post',
                data: JSON.stringify({ star: star_value, id: post_id }),
                type: 'POST',
                dataType: 'json',
                success: function(json) {
                    star.id = 'star_' + (star_value?'1':'0') +'_'+ post_id;
                    $('#star_img_'+ post_id).attr('src',  star_value ?
                        '/static/images/star_full.png' :
                        '/static/images/star_empty.png');
                },
                error: function(xhr, status) {
                    alert('Error staring post');
                }
            });
        });
    });
    </script>
</body>
</html>
