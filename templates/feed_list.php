<?php include 'header_block.php' ?>

<p><a href="/login?logout">Logout</a></p>
<h1>Reader v1.0</h1>
<ul>
<?php foreach ( $feeds as $feed ) : ?>
    <li><a href="/feed/<?php echo $feed->id ?>/articles?page=1"><?php echo $feed->name ?></a> (<?php echo $feed->unread ?>)</li>
<?php endforeach ?>
</ul>

<?php include 'footer_block.php' ?>
