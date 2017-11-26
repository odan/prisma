<?php

require_once __DIR__ . '/../config/bootstrap.php';

$container = container();

/* @var \Slim\Views\Twig $twig */
$twig = $container->get(\Slim\Views\Twig::class);

$settings = $container->get('settings');
$viewPath = $settings['twig']['path'];
$cachePath = $settings['twig']['cache_path'];

// Delete old twig cache files
rrmdir($cachePath);

// Iterate over all your templates and force compilation
$twig->getEnvironment()->disableDebug();
$twig->getEnvironment()->enableAutoReload();
$twig->getEnvironment()->setCache($cachePath);

$directory = new RecursiveDirectoryIterator($viewPath, FilesystemIterator::SKIP_DOTS);
foreach (new RecursiveIteratorIterator($directory, RecursiveIteratorIterator::SELF_FIRST) as $file) {
    /* @var SplFileInfo $file */
    if ($file->isFile() && $file->getExtension() === 'twig') {
        $templateName = substr($file->getPathname(), strlen($viewPath) + 1);
        $templateName = str_replace('\\', '/', $templateName);
        echo sprintf("Parsing: %s\n", $templateName);
        $twig->getEnvironment()->loadTemplate($templateName);
    }
}

/**
 * Remove directory recursively.
 *
 * @param string $path Path
 * @return bool True on success or false on failure.
 */
function rrmdir($path) {
    $files = glob($path . '/*');
    foreach ($files as $file) {
        is_dir($file) ? rrmdir($file) : unlink($file);
    }
    return rmdir($path);
}

echo "Done\n";