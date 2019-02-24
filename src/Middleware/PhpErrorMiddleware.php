<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
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
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $this->container->get(LoggerInterface::class);

        // Set custom php error handler for minor errors
        set_error_handler(function ($errorCode, $message, $file, $line) use ($logger) {
            $errorMessage = "Error number [$errorCode] $message on line $line in file $file";
            switch ($errorCode) {
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_PARSE:
                    $logger->error($errorMessage);
                    break;
                case E_USER_WARNING:
                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                    $logger->warning($errorMessage);
                    break;
                case E_USER_NOTICE:
                case E_STRICT:
                case E_DEPRECATED:
                    $logger->notice($errorMessage);
                    break;
                default:
                    $logger->notice($errorMessage);
                    break;
            }

            // Optional: Write error to response
            //$response = $response->getBody()->write("Error: [$errorCode] $message<br>\n");

            // Don't execute PHP internal error handler
            return true;
        }, E_NOTICE | E_STRICT);

        return $next($request, $response);
    }
}
