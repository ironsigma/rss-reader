<?php include 'header_block.php' ?>

<p><a href="/login?logout">Logout</a></p>
<h1>RSS Reader</h1>
<ul style="list-style-type:none">
    <li><img style="width:20px;vertical-align:top" src="/static/images/rss_folder.png" /> <a href="/feed/stared/articles?page=1">Stared Items</a> (<?php echo $stared_count ?>)</li>
<?php foreach ( $feeds as $feed ) : ?>
    <li><img style="width:20px;vertical-align:top" src="/static/images/rss_feed.png" /> <a href="/feed/<?php echo $feed->id ?>/articles?page=1"><?php echo $feed->name ?></a> (<?php echo $feed->unread ?>)</li>
<?php endforeach ?>
</ul>

<?php include 'footer_block.php' ?>
