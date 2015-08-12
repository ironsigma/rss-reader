<?php $page_css = array('jquery.treeview.css', 'reader.less', 'feeds.less') ?>
<?php $page_js = array('jquery.js', 'less.js', 'jquery.treeview.js') ?>
<?php include Template::file('layout/header') ?>

<?php
function selfURL() {
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; 
    $protocol = substr(strtolower($_SERVER["SERVER_PROTOCOL"]), 0, strpos($_SERVER["SERVER_PROTOCOL"], '/')).$s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
}
?>

<div id="container">
    <div id="header">
        <h1>RSS Reader</h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a href="/stats">Stats</a></li>
            <li><a href="/feedConfig">Feeds</a></li>
            <li><a href="/folderConfig">Folders</a></li>
            <li><a class="right" href="/login?logout">Logout</a></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <div id="feed-list">
            <ul id="tree" class="feedtree">
                <?php if ( $stared_count != 0 ) : ?>
                <li><span class="folder star-folder"><a href="/feed/stared/articles?page=1">Stared Items</a> (<?php echo $stared_count ?>)</span></li>
                <?php endif ?>
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
        </div>
    </div><!-- content -->
    <div id="footer">
        &copy; 2013 HawkPrime.com All Right Reserved
    </div><!-- footer -->
</div><!-- container -->

<script type="text/javascript">
$(document).ready(function(){
    $('#tree').treeview({
        animated: 'fast',
        collapsed: true
    });
    setInterval(function(){
        window.location.reload("<?php echo selfURL() ?>");
    }, 30 * 60000); // every 30 mins
});
</script>
<?php include Template::file('layout/footer') ?>
