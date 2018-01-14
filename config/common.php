<?php

/**
 * Services and helper functions
 */

use Slim\App;
use Symfony\Component\Translation\Translator;

/**
 * App instance.
 *
 *
 * @param App|null $app
 * @return App
 */
function app(App $app = null)
{
    static $instance = null;
    if ($app !== null) {
        $instance = $app;
    }
    if($instance === null) {
        throw new RuntimeException('App not defined');
    }
    return $instance;
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
