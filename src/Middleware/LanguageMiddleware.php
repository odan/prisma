<?php

namespace App\Middleware;

use App\Domain\User\Locale;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Middleware.
 */
class LanguageMiddleware
{
    /**
     * @var Locale
     */
    protected $locale;

    /**
     * Constructor.
     *
     * @param Locale $locale
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
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
        // Get user language
        $locale = $this->locale->getLocale();
        $domain = $this->locale->getDomain();

        // Default language
        if (empty($locale)) {
            $locale = 'en_US';
            $domain = 'messages';
        }

        // Set language
        $this->locale->setLanguage($locale, $domain);

        return $next($request, $response);
    }
}
