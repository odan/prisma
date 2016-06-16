<?php

namespace App\Middleware;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * LoggerMiddleware
 */
class LoggerMiddleware
{

    const ATTRIBUTE = 'logger';

    /**
     * Settings
     *
     * @var array
     */
    protected $config;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
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
        $logger = new Logger('app');
        if (isset($this->config['level'])) {
            $level = $this->config['level'];
        } else {
            $level = Logger::ERROR;
        }
        $logDir = $this->config['path'];
        $logFile = $logDir . '/log.txt';
        $handler = new RotatingFileHandler(
                $logFile, 0, $level, true, 0775
        );
        $logger->pushHandler($handler);

        // Put service container to request object
        $request = $request->withAttribute(static::ATTRIBUTE, $logger);

        return $next($request, $response);
    }
}
