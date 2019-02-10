<?php

namespace App\Middleware;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Handlers\PhpError;
use Throwable;

/**
 * Application Error handler.
 */
final class ErrorHandler extends PhpError
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Constructor.
     *
     * @param Logger $logger The logger
     * @param bool $displayErrorDetails Set to true to display full details
     */
    public function __construct(Logger $logger, bool $displayErrorDetails = false)
    {
        parent::__construct($displayErrorDetails);
        $this->logger = $logger;
    }

    /**
     * Invoke.
     *
     * @param Request $request
     * @param Response $response
     * @param Throwable $exception
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, Throwable $exception): Response
    {
        // Log the message
        $context = $this->renderArrayError($exception);
        $this->logger->error($exception->getMessage(), $context);

        return parent::__invoke($request, $response, $exception);
    }

    /**
     * Render error as Text.
     *
     * @param Throwable $error
     *
     * @return mixed[] Error data
     */
    protected function renderArrayError(Throwable $error): array
    {
        return [
            'type' => get_class($error),
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
        ];
    }
}
