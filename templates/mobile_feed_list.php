<?php include 'mobile_header.php' ?>

<div data-role="page">
    <div data-role="header">
        <h1>RSS Reader</h1>
    </div>

    <div data-role="content">
        <ul data-role="listview">
            <li data-icon="star"><a href="/feed/stared/articles?mobi&amp;page=1">Stared Items <span class="ui-li-count"><?php echo $stared_count ?></span></a></li>
            <li data-role="list-divider">All Feeds</li>
            <?php $last_folder = '' ?>
            <?php foreach ( $feeds as $feed ) : ?>
            <?php if ( $feed->folder && $last_folder != $feed->folder ) : ?>
                <?php $last_folder = $feed->folder ?>
                <li data-role="list-divider"><?php echo $feed->folder ?></li>
            <?php endif ?>
            <li><a href="/feed/<?php echo $feed->id ?>/articles?mobi&amp;page=1"><?php echo $feed->name ?> <span class="ui-li-count"><?php echo $feed->unread ?></span></a></li>
            <?php endforeach ?>
        </ul>
    </div>

    <!--
    <div data-role="footer">
        <h4>Page Footer</h4>
    </div>
    -->
</div>


<?php include 'mobile_footer.php' ?>
