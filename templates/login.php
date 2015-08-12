<?php $page_css = array('reader.less') ?>
<?php $page_js = array('less.js') ?>
<?php include Template::file('layout/header') ?>

<div id="container">
    <div id="header">
        <h1>RSS Reader</h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a class="right" href="/login">Login</a></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <?php if ($message) : ?>
        <p><?php echo $message ?></p>
        <?php endif ?>
        <form name="login" action="/login<?php echo ($reg?'?register':'') ?>" method="post">
            Username: <input type="text" name="username" maxlength="30"/><br/>
            Password: <input type="password" name="password" /><br/>
            <?php if ( $reg ) : ?>
            Password Again: <input type="password" name="password2" /><br/>
            <?php endif ?>
            <input type="submit" value="<?php echo ($reg?'Register':'Login') ?>" />
        </form>
    </div><!-- content -->
    <div id="footer">
        &copy; 2013 HawkPrime.com All Right Reserved
    </div><!-- footer -->
</div><!-- container -->

<?php include Template::file('layout/footer') ?>
