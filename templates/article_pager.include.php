<div id="pager">
<?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
    <a href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=1&amp;ids=all">&times;&times;Mark All Read</a>
<?php endif ?>
<?php if ($page > 1) : ?>
    <a style="margin-left:10px" href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $page - 1 ?>">&laquo; Prev</a>
<?php endif ?>
<span style="margin-left:10px">Page <?php echo $page ?> of <?php echo $page_count ?></span>
<?php /*
<?php for ( $i = 1; $i <= $page_count; $i ++ ) : ?>
    <?php if ($i == $page) : ?>
        <?php echo $i ?>
    <?php else: ?>
        <a href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $i ?>"><?php echo $i ?></a>
    <?php endif ?>
<?php endfor ?>
*/ ?>
<?php if ($page < $page_count) : ?>
    <a style="margin-left:10px" href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $page + 1 ?>">Next &raquo;</a>
<?php endif ?>
<?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
    <a style="margin-left:10px" href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $article_ids ?>">&times; Mark Page Read</a>
<?php endif ?>
</div>
