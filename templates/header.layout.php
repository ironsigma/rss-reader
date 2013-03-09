<!doctype html>
<html lang="en">
<head>
    <title>RSS Reader<?php echo isset($page_title) ? $page_title : '' ?></title>
    <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="/static/images/favicon.ico">
    <?php if ( isset($page_css) ) foreach ( $page_css as $style ) : ?>
    <link type="text/css" rel="stylesheet<?php echo (substr_compare($style, '.less', -5, 5)===0?'/less':'') ?>" href="/static/css/<?php echo $style ?>">
    <?php endforeach ?>
    <?php if ( isset($page_js) ) foreach ( $page_js as $script ) : ?>
    <script type="text/javascript" src="/static/js/<?php echo $script ?>"></script>
    <?php endforeach ?>
</head>
<body>
