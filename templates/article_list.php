<?php $page_title = ' - '. $feed->name .' ('. $article_count .')' ?>
<?php $page_css = array('article.css') ?>
<?php $page_js = array('jquery.js') ?>
<?php include 'header_block.php' ?>

<a href="/">Back To Feed List</a>
<h1><?php echo $feed->name ?> (<?php echo $article_count ?>)</h1>
<?php if ( $article_count === 0 ) : ?>
<p>No unread items.</p>
<?php else : ?>
<?php include 'article_pager.php' ?>
<?php foreach($articles as $a) : ?>
<div class='article'>
    <div id="star_<?php echo $a->stared?'1':'0' ?>_<?php echo $a->id ?>" class='star'><img id="star_img_<?php echo $a->id ?>" src="/static/images/star_<?php echo $a->stared?'full':'empty' ?>.png"/></div>
    <h2><a href="<?php echo $a->link ?>"><?php echo $a->title ?></a></h2>
    <h3><?php echo date('D M j, Y &\m\d\a\s\h; g:i a', $a->ts) ?></h3>
    <div class='text'><?php echo $a->text ?></div>
</div>
<?php endforeach ?>
<?php include 'article_pager.php' ?>
<?php endif ?>
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
<div id="footer">&copy; 2013 AxiSym3.net All Right Reserved</div>

<?php include 'footer_block.php' ?>
