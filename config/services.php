<?php

/**
 * Services and helper functions
 */

use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Translator;

/**
 * App instance.
 *
 * @param ContainerInterface|array $container Either a ContainerInterface or an associative array of app settings
 * @return \Slim\App
 */
function app($container = [])
{
    static $slim = null;
    if ($slim === null) {
        $slim = new \Slim\App($container);
    }
    return $slim;
}

/**
 * A simple PHP Dependency Injection Container.
 *
 * @return \Slim\Container
 */
function container()
{
    return app()->getContainer();
}

/**
 * Get application settings.
 *
 * @return \Slim\Collection
 */
function settings()
{
    return container()->get('settings');
}

/**
 * Text translation (I18n)
 *
 * @param string $message
 * @param ...$context
 * @return string
 *
 * <code>
 * echo __('Hello');
 * echo __('There are %s persons logged', 7);
 * </code>
 */
function __($message)
{
    /* @var $translator Translator */
    $translator = container()->get('translator');
    $translated = $translator->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}
