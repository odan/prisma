<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <base href="<?= $baseurl ?>" />
        <link rel="shortcut icon" href="icons/favicon.ico">
        <title><?= $this->e(__('Sign in')); ?></title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/ie10-viewport-bug-workaround.js"></script>
        <?php echo $this->assets(['view::Login/login.css'], ['inline' => false, 'public' => true]); ?>
        <?php
        if (!empty($text)) :
            echo sprintf("<script>\$d.addText(%s);</script>", json_encode($text));
        endif;
        ?>
    </head>
    <body>
        <div class="container">
            <form class="form-signin" method="POST">
                <h2 class="form-signin-heading"><?= $this->e(__('Please sign in')); ?></h2>
                <input type="text" name="username" class="form-control" placeholder="<?= $this->e(__('Username')); ?>" autofocus>
                <input type="password" name="password" class="form-control" placeholder="<?= $this->e(__('Password')); ?>">
                <!--<label class="checkbox">
                    <input type="checkbox" value="remember-me"> Remember me
                </label>-->
                <button id="btn_login" class="btn btn-lg btn-primary btn-block" type="submit"><?= $this->e(__('Sign in')); ?></button>
            </form>
        </div>
    </body>
</html>
