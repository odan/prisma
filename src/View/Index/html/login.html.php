<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <base href="<?= $baseurl ?>" />
        <link rel="shortcut icon" href="assets/ico/favicon.ico">
        <title><?php wh(__('Sign in')); ?></title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
        <?php echo $this->assetCss($assets, ['inline' => false]); ?>
        <?php echo $this->assetJs($assets, ['inline' => false]); ?>
    </head>

    <body>
        <div class="container">
            <form class="form-signin" method="POST">
                <h2 class="form-signin-heading"><?php wh(__('Please sign in')); ?></h2>
                <input type="text" name="username" class="form-control" placeholder="<?php wh(__('Username')); ?>" autofocus>
                <input type="password" name="password" class="form-control" placeholder="<?php wh(__('Password')); ?>">
                <!--<label class="checkbox">
                    <input type="checkbox" value="remember-me"> Remember me
                </label>-->
                <button id="btn_login" class="btn btn-lg btn-primary btn-block" type="submit"><?php wh(__('Sign in')); ?></button>
            </form>
        </div>
    </body>
</html>
