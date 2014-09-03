<?php $page_title = ' - '. $feed_name .' ('. $article_count .')' ?>
<?php $page_css = array('reader.less', 'article_list.less') ?>
<?php $page_js = array('jquery.js', 'less.js') ?>
<?php include Template::file('layout/header') ?>

<div id="container">
    <div id="header">
        <h1><?php echo $feed_name ?> <span id="header-count">(<?php echo $article_count ?>)</span></h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a href="/">Feeds</a></li>
            <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
                <li><a class="right" href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $article_ids ?>">Page Read</a></li>
                <li><a class="right" href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=1&amp;ids=all">All Read</a></li>
            <?php endif ?>
            <li><span class="right">Page <?php echo $page ?> of <?php echo $page_count ?></span></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <?php if ( $article_count === 0 ) : ?>
        <p>No unread items.</p>
        <?php else : ?>
        <?php foreach($articles as $a) : ?>
        <div class='article'>
            <div class="read"><a href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $a->id ?>"><img src="/static/images/read.png"/></a></div>
            <div id="star_<?php echo $a->stared?'1':'0' ?>_<?php echo $a->id ?>" class='star'><img id="star_img_<?php echo $a->id ?>" src="/static/images/star_<?php echo $a->stared?'full':'empty' ?>.png"/></div>
            <?php if ( $a->feed ) : ?>
            <div class="feed"><?php echo $a->feed ?></div>
            <?php endif ?>
            <h2><a target="_blank" href="/link?post=<?php echo $a->id ?>&feed=<?php echo $feed_id ?>&url=<?php echo Base64::encode($a->link) ?>"><?php echo $a->title ?></a></h2>
            <hr/>
            <span class="calendar"><?php echo date("j", $a->published) ?></span>
            <h3><?php echo date('D M j, Y &\m\d\a\s\h; g:i a', $a->published) ?></h3>
            <div class='text'><?php echo $a->text ?></div>
        </div>
        <?php endforeach ?>
        <div id="pager">
        <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
            <a href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=1&amp;ids=all">&times;&times;Mark All Read</a>
        <?php endif ?>
        <?php if ($page > 1) : ?>
            <a style="margin-left:10px" rel="prev" href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $page - 1 ?>">&laquo; Prev</a>
        <?php endif ?>
        <span style="margin-left:10px">Page <?php echo $page ?> of <?php echo $page_count ?></span>
        <?php if ($page < $page_count) : ?>
            <a style="margin-left:10px" rel="next" href="/<?php echo $container ?>/<?php echo $feed_id ?>/articles?page=<?php echo $page + 1 ?>">Next &raquo;</a>
        <?php endif ?>
        <?php if ($article_count > 0 && $feed_id !== 'stared') : ?>
            <a style="margin-left:10px" href="/feed/<?php echo $feed_id ?>/read?<?php echo $container ?>=<?php echo $feed_id ?>&amp;page=<?php echo $page ?>&amp;ids=<?php echo $article_ids ?>">&times; Mark Page Read</a>
        <?php endif ?>
        </div>
        <?php endif ?>
    </div><!-- content -->
    <div id="footer">
        &copy; 2013 AxiSym3.net All Right Reserved
    </div><!-- footer -->
</div><!-- container -->

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

<?php include Template::file('layout/footer') ?>
