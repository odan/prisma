<?php

/**
 * Services and helper functions
 */

use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Symfony\Component\Translation\MessageSelector;
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
 * @return \Slim\Container|ContainerInterface
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
 * Translator
 *
 * @return Translator
 */
function translator()
{
    return container()->get('translator');
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
    $translated = translator()->trans($message);
    $context = array_slice(func_get_args(), 1);
    if (!empty($context)) {
        $translated = vsprintf($translated, $context);
    }
    return $translated;
}

/**
 * Set locale
 *
 * @param string $locale
 * @param string $domain
 * @return void
 */
function set_locale($locale = 'en_US', $domain = 'messages')
{
    $settings = container()->get('settings');
    $moFile = sprintf('%s/%s_%s.mo', $settings['locale_path'], $locale, $domain);

    $translator = new Translator($locale, new MessageSelector());
    $translator->addLoader('mo', new MoFileLoader());

    $translator->addResource('mo', $moFile, $locale, $domain);
    $translator->setLocale($locale);

    // Inject translator into function
    container()->offsetSet('translator', $translator);
}

/**
 * Generates a normalized URI for the given path.
 *
 * @param Request $request
 * @param string $path A path to use instead of the current one
 * @param bool $full return absolute or relative url
 * @return string The normalized URI for the path
 */
function baseurl(Request $request, $path = '', $full = false)
{
    $http = new \App\Util\Http($request);
    return $http->getBaseUrl($path, $full);
}