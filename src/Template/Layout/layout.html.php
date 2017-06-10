<!DOCTYPE html>
<html lang="<?= $this->e(__('en')); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <base href="<?= $baseUrl; ?>"/>
    <link rel="shortcut icon" href="icons/favicon.ico">
    <title><?= $this->e(__('Demo')); ?></title>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="all"/>
    <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css" media="all"/>
    <?=
    $this->assets([
        'assets::js/jquery.min.js',
        'assets::js/bootstrap.min.js',
        'assets::js/mustache.min.js'], ['inline' => false, 'name' => 'jquery-bootstrap.js']);
    ?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <?=
    $this->assets([
        'assets::css/d.css',
        'assets::css/notifIt-app.css',
        'view::Layout/layout.css',
        'view::Layout/print.css'], ['inline' => false, 'name' => 'layout.css']);
    ?>
    <?=
    $this->assets([
        'assets::js/d.js',
        'assets::js/notifIt.js',
        'view::Layout/app.js'], ['inline' => false, 'name' => 'layout.js']);
    ?>
    <?= $this->section('assets') ?>
    <?php
    if (!empty($text)) :
        echo sprintf('<script>$d.addText(%s)</script>', json_encode($text));
    endif;
    ?>
</head>
<body>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only"><?= $this->e(__('Toggle navigation')); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><?= $this->e(__('Project name')); ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#"><?= $this->e(__('Home')); ?></a></li>
                <li><a href="#about"><?= $this->e(__('About')); ?></a></li>
                <li><a href="#contact"><?= $this->e(__('Contact')); ?></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-expanded="false"><?= $this->e(__('Dropdown')); ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#"><?= $this->e(__('Action')); ?></a></li>
                        <li><a href="#"><?= $this->e(__('Another action')); ?></a></li>
                        <li><a href="#"><?= $this->e(__('Something else here')); ?></a></li>
                        <li class="divider"></li>
                        <li class="dropdown-header"><?= $this->e(__('Nav header')); ?></li>
                        <li><a href="#"><?= $this->e(__('Separated link')); ?></a></li>
                        <li><a href="#"><?= $this->e(__('One more separated link')); ?></a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#settings"><?= $this->e(__('Settings')); ?></a></li>
                <li><a href="login"><?= $this->e(__('Logout')); ?></a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>
<?= $this->section('content') ?>
</body>
</html>
