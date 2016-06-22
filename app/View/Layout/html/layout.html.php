<!DOCTYPE html>
<html lang="<?php wh(__('en')); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <base href="<?= $baseurl; ?>" />
        <link rel="shortcut icon" href="assets/ico/favicon.ico">
        <title><?php wh(__('Demo')); ?></title>
        <link type="text/css" href="assets/css/bootstrap.min.css" rel="stylesheet" />
        <link type="text/css" href="assets/css/font-awesome.min.css" rel="stylesheet">
        <?= $this->assetCss($assets, ['inline' => false]); ?>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <!-- Fixed navbar -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only"><?php wh(__('Toggle navigation')); ?></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#"><?php wh(__('Project name')); ?></a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#"><?php wh(__('Home')); ?></a></li>
                        <li><a href="#about"><?php wh(__('About')); ?></a></li>
                        <li><a href="#contact"><?php wh(__('Contact')); ?></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php wh(__('Dropdown')); ?> <span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#"><?php wh(__('Action')); ?></a></li>
                                <li><a href="#"><?php wh(__('Another action')); ?></a></li>
                                <li><a href="#"><?php wh(__('Something else here')); ?></a></li>
                                <li class="divider"></li>
                                <li class="dropdown-header"><?php wh(__('Nav header')); ?></li>
                                <li><a href="#"><?php wh(__('Separated link')); ?></a></li>
                                <li><a href="#"><?php wh(__('One more separated link')); ?></a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#settings"><?php wh(__('Settings')); ?></a></li>
                        <li><a href="login"><?php wh(__('Logout')); ?></a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <?= $this->fetch($content, $this->data) ?>
        <!-- JavaScript -->
        <script type="text/javascript" src="assets/js/jquery.min.js"></script>
        <script type="text/javascript" src="assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="assets/js/ie10-viewport-bug-workaround.js"></script>
        <?= $this->assetJs($assets, ['inline' => false]); ?>
        <?php
        if (!empty($text)) :
            echo sprintf("<script>\$d.addText(%s);</script>", json_encode($text));
        endif;
        ?>
    </body>
</html>