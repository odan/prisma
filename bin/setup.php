<?php

//
// Setup
//
$installDir = __DIR__;
$version = 'master';

// Alternative
// Downlod // https://github.com/odan/psr7-full-stack/archive/0.0.1.zip
// Extract zip, move install folder to to install-dir
// Run composer install

$dirs = array();
$dirs[] = array('path' => 'bin', 'chmod' => null);
$dirs[] = array('path' => 'src', 'chmod' => null);
$dirs[] = array('path' => 'src/Config', 'chmod' => null);
$dirs[] = array('path' => 'src/Controller', 'chmod' => null);
$dirs[] = array('path' => 'src/Locale', 'chmod' => null);
$dirs[] = array('path' => 'src/Migration', 'chmod' => null);
$dirs[] = array('path' => 'src/Model', 'chmod' => null);
$dirs[] = array('path' => 'src/View', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Index', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Index/css', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Index/html', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Index/js', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Layout', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Layout/css', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Layout/html', 'chmod' => null);
$dirs[] = array('path' => 'src/View/Layout/js', 'chmod' => null);
$dirs[] = array('path' => 'web', 'chmod' => null);
$dirs[] = array('path' => 'web/img', 'chmod' => null);
$dirs[] = array('path' => 'web/css', 'chmod' => null);
$dirs[] = array('path' => 'web/js', 'chmod' => null);
$dirs[] = array('path' => 'tmp', 'chmod' => 0775);
$dirs[] = array('path' => 'tmp/cache', 'chmod' => 0775);
$dirs[] = array('path' => 'tmp/log', 'chmod' => 0775);

foreach ($dirs as $dir) {
    $fullPath = sprintf('%s/%s', $installDir, $dir['path']);
    $chmod = isset($dir['chmod']) ? $dir['chmod'] : 0775;
    if (!file_exists($fullPath)) {
        mkdir($fullPath, $chmod);
    } else {
        chmod($fullPath, $chmod);
    }
}

echo "Download composer\n";
$content = file_get_contents('https://getcomposer.org/composer.phar');
$composerFile = sprintf('%s/composer.phar', $installDir);
file_put_contents($composerFile, $content);

echo "Download composer.json\n";
$content = file_get_contents("https://raw.githubusercontent.com/odan/psr7-full-stack/$version/composer.json");
file_put_contents(sprintf('%s/composer.json', $installDir), $content);

echo "Install composer.json\n";
system(sprintf('php %s/composer.phar self-update', $installDir));
system(sprintf('php %s/composer.phar install', $installDir, $installDir));

if (file_exists($composerFile)) {
    unlink($composerFile);
}