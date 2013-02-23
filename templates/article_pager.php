<div>
Page <?php echo $page ?> of <?php echo $page_count ?> :
<?php if ($page > 1) : ?>
    <a href="/feed/<?php echo $feed_id ?>/articles?page=<?php echo $page - 1 ?>">&laquo; Prev</a>
<?php endif ?>
<?php for ( $i = 1; $i <= $page_count; $i ++ ) : ?>
    <?php if ($i == $page) : ?>
        <?php echo $i ?>
    <?php else: ?>
        <a href="/feed/<?php echo $feed_id ?>/articles?page=<?php echo $i ?>"><?php echo $i ?></a>
    <?php endif ?>
<?php endfor ?>
<?php if ($page < $page_count) : ?>
    <a href="/feed/<?php echo $feed_id ?>/articles?page=<?php echo $page + 1 ?>">Next &raquo;</a>
<?php endif ?>
<?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
<a style="margin-left:20px" href="/feed/<?php echo $feed_id ?>/read?feed=<?php echo $feed_id ?>&page=<?php echo $page ?>&ids=<?php echo $article_ids ?>">&times; Mark Page Read</a>
<a style="margin-left:20px" href="/feed/<?php echo $feed_id ?>/read?feed=<?php echo $feed_id ?>&page=1&ids=all">&times;&times; Mark All Read</a>
<?php endif ?>
</div>
