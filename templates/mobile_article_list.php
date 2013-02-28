<?php include 'mobile_header.php' ?>

<div data-role="page">
    <div data-role="panel" id="pager" data-display="overlay">
        <ul data-role="listview">
        <li data-role="list-divider">Navigate Page</li>
        <li data-icon="arrow-l" data-iconpos="left"><a href="/?mobi">All Feeds</a></li>
        <?php if ($page > 1) : ?>
            <li data-icon="arrow-l" data-iconpos="left"><a href="/feed/<?php echo $feed_id ?>/articles?mobi&amp;page=<?php echo $page - 1 ?>">Prev</a></li>
        <?php endif ?>
        <?php if ($page < $page_count) : ?>
            <li data-icon="arrow-r"><a href="/feed/<?php echo $feed_id ?>/articles?mobi&amp;page=<?php echo $page + 1 ?>">Next</a></li>
        <?php endif ?>
        <li data-role="list-divider">Articles</li>
        <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
            <li data-icon="check" data-iconpos="left"><a href="/feed/<?php echo $feed_id ?>/read?mobi&amp;feed=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $article_ids ?>">Mark Page Read</a></li>
        <?php endif ?>
        <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
            <li data-icon="delete" data-iconpos="left"><a href="/feed/<?php echo $feed_id ?>/read?mobi&amp;feed=<?php echo $feed_id ?>&amp;page=1&amp;ids=all">Mark All Read</a></li>
        <?php endif ?>
        <li data-role="list-divider">Jump To Page</li>
        <?php for ( $i = 1; $i <= $page_count; $i ++ ) : ?>
            <li><a href="/feed/<?php echo $feed_id ?>/articles?mobi&amp;page=<?php echo $i ?>">Page <?php echo $i ?></a></li>
        <?php endfor ?>
        </ul>
    </div>

    <div data-role="header">
        <a href="#pager" data-icon="bars">Menu</a>
        <h1><?php echo $feed_name ?> (<?php echo $article_count ?>)</h1>
        <a data-icon="arrow-l" href="/?mobi">All Feeds</a>
    </div>

    <div data-role="content">
        <ul data-role="listview">
            <?php foreach($articles as $a) : ?>
<?php /*
            <li data-role="list-divider"><?php echo $a->title ?></li>
 */ ?>
            <li><a href="<?php echo $a->link ?>"><?php echo $a->title ?></a></li>
            <li data-icon="<?php echo $a->stared?'star':'plus' ?>">
                <?php echo $a->text ?>
                 <p class="ui-li-aside"><?php echo date('M j, Y g:i a', $a->published) ?></p>
            </li>
            <?php endforeach ?>
        </ul>
    </div>

    <div data-role="footer">
        <a data-icon="arrow-l" href="/?mobi">Back To All Feeds</a>
        <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
        <a data-icon="delete"href="/feed/<?php echo $feed_id ?>/read?mobi&amp;feed=<?php echo $feed_id ?>&amp;page=1&amp;ids=all">All</a>
        <?php endif ?>
        <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
        <a href="/feed/<?php echo $feed_id ?>/read?mobi&amp;feed=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $article_ids ?>" data-icon="check">Mark Page Read</a>
        <?php endif ?>
    </div>
</div>

<?php include 'mobile_footer.php' ?>
