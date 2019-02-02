<?php

namespace App\Middleware;

use App\Domain\User\Locale;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container;

/**
 * Middleware.
 */
class LanguageMiddleware
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Invoke middleware.
     *
     * @param  ServerRequestInterface $request PSR7 request
     * @param  ResponseInterface $response PSR7 response
     * @param  callable $next Next middleware
     *
     * @throws ContainerException
     *
     * @return ResponseInterface PSR7 response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        $localisation = $this->container->get(Locale::class);

        // Get user language
        $locale = $localisation->getLocale();
        $domain = $localisation->getDomain();

        // Default language
        if (empty($locale)) {
            $locale = 'en_US';
            $domain = 'messages';
        }

        // Set language
        $localisation->setLanguage($locale, $domain);

        return $next($request, $response);
    }
}
