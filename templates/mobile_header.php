<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Mobile RSS Reader<?php echo isset($page_title) ? $page_title : '' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="/static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="/static/css/jquery.mobile.css" />
    <?php if ( isset($page_css) ) foreach ( $page_css as $style ) : ?>
    <link rel="stylesheet" type="text/css" href="/static/css/<?php echo $style ?>">
    <?php endforeach ?>
    <script type="text/javascript" src="/static/js/jquery.js"></script>
    <script type="text/javascript" src="/static/js/jquery.mobile.js"></script>
    <?php if ( isset($page_js) ) foreach ( $page_js as $script ) : ?>
    <script type="text/javascript" src="/static/js/<?php echo $script ?>"></script>
    <?php endforeach ?>
</head>
<body>
