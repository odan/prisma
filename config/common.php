<?php

/**
 * Services and helper functions
 */

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Translation\Translator;

/**
 * App instance.
 *
 * @return \Slim\App
 */
function app()
{
    static $app = null;
    if ($app === null) {
        $settings = read(__DIR__ . '/config.php');
        $app = new \Slim\App(["settings" => $settings]);
    }
    return $app;
}

/**
 * Text translation (I18n)
 *
 * @param string $message
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s users logged in.', 7);
 * </code>
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function __($message)
{
    /* @var $translator Translator */
    $translator = app()->getContainer()->get(Translator::class);
    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}
