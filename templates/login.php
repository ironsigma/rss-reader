<!doctype html>
<html lang="en">
<head>
    <title>RSS Reader</title>
    <meta charset="utf-8" />
</head>
<body>
    <h1>RSS Reader</h1>
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
</body>
</html>
