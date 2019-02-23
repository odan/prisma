<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use RuntimeException;
use Slim\Container;

/**
 * Middleware to handle minor PHP errors.
 */
class PhpErrorMiddleware
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
     * @return ResponseInterface PSR7 response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        // Set custom php error handler for minor errors
        set_error_handler(function ($errorCode, $message, $file, $line) {
            // Convert PHP error to runtime exception
            $exception = new RuntimeException($message, $errorCode);
            $reflectionClass = new ReflectionClass(RuntimeException::class);
            $property = $reflectionClass->getProperty('file');
            $property->setAccessible(true);
            $property->setValue($exception, $file);

            $property = $reflectionClass->getProperty('line');
            $property->setAccessible(true);
            $property->setValue($exception, $line);

            throw $exception;
        }, E_ALL);

        return $next($request, $response);
    }
}
