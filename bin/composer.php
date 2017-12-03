<?php

$event = isset($argv[1]) ? $argv[1] : null;

if ($event == 'post-update-cmd') {
    // composer update
    update_assets();
}

if ($event == 'post-install-cmd') {
    // composer install
    update_assets();
}

if ($event == 'update-assets') {
    update_assets();
}

function update_assets()
{
    echo "Updating assets...\n";

    $files = [];

    // Bootstrap
    $files[] = [__DIR__ . '/../public/css/bootstrap.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css'];
    $files[] = [__DIR__ . '/../public/css/bootstrap.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css'];
    $files[] = [__DIR__ . '/../public/css/bootstrap.min.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'];
    $files[] = [__DIR__ . '/../public/css/bootstrap-theme.min.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css'];
    $files[] = [__DIR__ . '/../public/css/bootstrap-theme.css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.css'];
    $files[] = [__DIR__ . '/../public/js/bootstrap.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js'];
    $files[] = [__DIR__ . '/../public/js/bootstrap.min.js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'];

    // jQuery
    $files[] = [__DIR__ . '/../public/js/jquery.js', 'https://code.jquery.com/jquery-3.2.1.js'];
    $files[] = [__DIR__ . '/../public/js/jquery.min.js', 'https://code.jquery.com/jquery-3.2.1.min.js'];
    $files[] = [__DIR__ . '/../public/js/jquery.min.map', 'https://code.jquery.com/jquery-3.2.1.min.map'];

    // mustache.js
    $files[] = [__DIR__ . '/../public/js/mustache.js', 'https://raw.githubusercontent.com/janl/mustache.js/v2.3.0/mustache.js'];
    $files[] = [__DIR__ . '/../public/js/mustache.min.js', 'https://raw.githubusercontent.com/janl/mustache.js/v2.3.0/mustache.min.js'];

    // Utils
    //$files[] = [__DIR__ . '/../public/js/sprintf.min.js', 'https://raw.githubusercontent.com/alexei/sprintf.js/master/dist/sprintf.min.js'];

    foreach ($files as $file) {
        file_put_contents($file[0], file_get_contents($file[1]));
    }

    echo "Done\n";
}