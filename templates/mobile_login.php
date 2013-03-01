<?php include 'mobile_header.php' ?>

<div data-role="page">

    <div data-role="header">
        <h1>RSS Reader</h1>
    </div>

    <div data-role="content">
        <form name="login" action="/login?mobi<?php echo ($reg?'&register':'') ?>" method="post">
            <ul data-role="listview" data-insert="true">
                <?php if ($message) : ?>
                <li><?php echo $message ?></li>
                <?php endif ?>
                <li data-role="fieldcontain">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" maxlength="30"/><br/>
                </li>
                <li data-role="fieldcontain">
                    <label for="password">Password:</label>
                    <input name="password" id="password" type="password"/><br/>
                </li>
                <?php if ( $reg ) : ?>
                <li data-role="fieldcontain">
                    <label for="password2">Password Again:</label>
                    <input type="password" id="password2" name="password2" /><br/>
                </li>
                <?php endif ?>
                <li data-role="fieldcontain">
                    <input type="submit" value="<?php echo ($reg?'Register':'Login') ?>" />
                </li>
            </ul>
        </form>
    </div>

</div>

<?php include 'mobile_footer.php' ?>
