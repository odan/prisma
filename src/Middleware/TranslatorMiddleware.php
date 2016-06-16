<?php

namespace App\Middleware;

use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use Symfony\Component\Translation\Loader\MoFileLoader;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * TranslatorMiddleware
 */
class TranslatorMiddleware
{

    /**
     * Attribute
     *
     * @var string Attribute
     */
    const ATTRIBUTE = 'translator';

    /**
     * Settings
     *
     * @var array
     */
    protected $options;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Invoke middleware.
     *
     * @param Request $request The request.
     * @param Response $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return Response A response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $request = $request->withAttribute(static::ATTRIBUTE, $this->create($request));
        return $next($request, $response);
    }

    /**
     * Create instance
     *
     * @param Request $request
     * @return Translator
     */
    public function create(Request $request)
    {
        $locale = $this->getLocale($request);
        $domain = 'messages';
        $translator = new Translator($locale, new MessageSelector());
        $translator->addLoader('mo', new MoFileLoader());

        // Set locale
        $moFile = sprintf('%s/../Locale/%s_%s.mo', __DIR__, $locale, $domain);
        if (file_exists($moFile)) {
            $translator->addResource('mo', $moFile, $locale, $domain);
            $translator->setLocale($locale);
        }

        // Inject translator into function
        __(null, null, $translator);

        //$test = __('Hello');
        return $translator;
    }

    /**
     * Get locale
     *
     * @param Request $request
     */
    public function getLocale(Request $request)
    {
        // Default
        $locale = 'en_US';

        // Get language from  session
        $session = $request->getAttribute(SessionMiddleware::ATTRIBUTE);
        if (empty($session)) {
            return $locale;
        }
        $sessionLocale = $session->get('locale');
        if (!empty($sessionLocale)) {
            $locale = $sessionLocale;
        }
        return $locale;
    }

}
