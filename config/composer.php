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

function update_assets() {
    echo "Updating assets\n";
    echo "  - Updating Bootstrap\n";
    file_put_contents(__DIR__ . '/../public/css/bootstrap.css', file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css'));
    file_put_contents(__DIR__ . '/../public/css/bootstrap.min.css', file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'));
    file_put_contents(__DIR__ . '/../public/css/bootstrap-theme.min.css', file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css'));
    file_put_contents(__DIR__ . '/../public/css/bootstrap-theme.css', file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.css'));
    file_put_contents(__DIR__ . '/../public/js/bootstrap.js', file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js'));
    file_put_contents(__DIR__ . '/../public/js/bootstrap.min.js', file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'));

    echo "  - Updating jQuery\n";
    file_put_contents(__DIR__ . '/../public/js/jquery.js', file_get_contents('https://code.jquery.com/jquery-3.2.1.js'));
    file_put_contents(__DIR__ . '/../public/js/jquery.min.js', file_get_contents('https://code.jquery.com/jquery-3.2.1.min.js'));
    file_put_contents(__DIR__ . '/../public/js/jquery.min.map', file_get_contents('https://code.jquery.com/jquery-3.2.1.min.map'));

    echo "  - Updating mustache.js\n";
    file_put_contents(__DIR__ . '/../public/js/mustache.js', file_get_contents('https://raw.githubusercontent.com/janl/mustache.js/v2.3.0/mustache.js'));
    file_put_contents(__DIR__ . '/../public/js/mustache.min.js', file_get_contents('https://raw.githubusercontent.com/janl/mustache.js/v2.3.0/mustache.min.js'));
}