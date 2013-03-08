<?php $page_css = array('jquery.treeview.css', 'reader.less', 'feeds.css') ?>
<?php $page_js = array('jquery.js', 'less.js', 'jquery.treeview.js') ?>
<?php include 'header_block.php' ?>

<div id="container">
    <div id="header">
        <h1>RSS Reader</h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a href="/stats">Feed Stats</a></li>
            <li><a class="right" href="/login?logout">Logout</a></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <ul id="tree" class="feedtree">
            <li><span class="folder"><a href="/feed/stared/articles?page=1">Stared Items</a> (<?php echo $stared_count ?>)</span></li>
        <?php $folder = '' ?>
        <?php foreach ( $feeds as $feed ) : ?>
            <?php if ( $feed->folder != $folder ) : ?>
                <?php if ( $folder !== '') : ?></ul></li><?php endif ?>
                <?php $folder = $feed->folder ?>
                <li><span class="folder"><a href="/folder/<?php echo $feed->folder_id ?>/articles?page=1"><?php echo $folder ?></a> (<?php echo $folder_counts[$feed->folder_id] ?>)</span><ul>
            <?php endif ?>
            <li><span class="feed"><a href="/feed/<?php echo $feed->id ?>/articles?page=1"><?php echo $feed->name ?></a> (<?php echo $feed->unread ?>)</span></li>
        <?php endforeach ?>
        <?php if ( $folder !== '') : ?></ul><?php endif ?>
        </ul>
    </div><!-- content -->
    <div id="footer">
        &copy; 2013 AxiSym3.net All Right Reserved
    </div><!-- footer -->
</div><!-- container -->

<script type="text/javascript">
$(document).ready(function(){
    $('#tree').treeview({
        animated: 'fast',
        collapsed: true
    });
});
</script>
<?php include 'footer_block.php' ?>
