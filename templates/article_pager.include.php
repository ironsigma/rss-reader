<div id="pager">
<?php if ($page > 1) : ?>
    <a href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $page - 1 ?>">&laquo; Prev</a>
<?php endif ?>
<?php for ( $i = 1; $i <= $page_count; $i ++ ) : ?>
    <?php if ($i == $page) : ?>
        <?php echo $i ?>
    <?php else: ?>
        <a href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $i ?>"><?php echo $i ?></a>
    <?php endif ?>
<?php endfor ?>
<?php if ($page < $page_count) : ?>
    <a href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $page + 1 ?>">Next &raquo;</a>
<?php endif ?>
    <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
        <?php if ( $container === 'feed' ) : ?>
        <a style="margin-left:20px" href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=1&amp;ids=all">&times;&times;Mark All Read</a>
        <?php endif ?>
        <a style="margin-left:20px" href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $article_ids ?>">&times; Mark Page Read</a>
    <?php endif ?>
</div>
