<?php

require_once __DIR__ . '/../config/bootstrap.php';

$container = app()->getContainer();

/* @var \Slim\Views\Twig $twigView */
$twigView = $container->get(\Slim\Views\Twig::class);

$settings = $container->get('settings');
$viewPath = $settings['twig']['path'];
$cachePath = $settings['twig']['cache_path'];

// Get the Twig Environment instance from the Twig View instance
$twig = $twigView->getEnvironment();
$twig->setCache($cachePath);

// Compile all Twig templates into cache directory
$compiler = new \Odan\Twig\TwigCompiler($twig, $cachePath);
$compiler->compile();

echo "Done\n";