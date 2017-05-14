<?php

namespace App\Middleware;

use Exception;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception as HttpException;
use League\Route\Route;
use League\Route\Strategy\StrategyInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HttpExceptionStrategy implements StrategyInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * @var Route
     */
    protected $route = null;

    /**
     * @var array
     */
    protected $events = array();

    /**
     * Set logger.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Register event.
     *
     * @param string $event
     * @param callable $callback
     * @return void
     */
    public function on($event, callable $callback)
    {
        $this->events[$event] = $callback;
    }

    /**
     * Raise event.
     *
     * @param string $event
     * @param array $args
     * @return mixed|null
     */
    protected function event($event, $args = [])
    {
        if (isset($this->events[$event])) {
            return call_user_func_array($this->events[$event], $args);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCallable(Route $route, array $vars)
    {
        $this->route = $route;

        return function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($route, $vars) {
            $request = $request->withAttribute('route', $route);

            $return = call_user_func_array($route->getCallable(), [$request, $response, $vars]);

            if (!$return instanceof ResponseInterface) {
                throw new RuntimeException(
                    'Route callables must return an instance of (Psr\Http\Message\ResponseInterface)'
                );
            }

            $response = $return;
            $response = $next($request, $response);

            return $response;
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getNotFoundDecorator(NotFoundException $exception)
    {
        // 404	Not Found
        return function (ServerRequestInterface $request, ResponseInterface $response) use ($exception) {
            return $this->buildHttpResponse($request, $response, $exception);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getMethodNotAllowedDecorator(MethodNotAllowedException $exception)
    {
        // 405	Method Not Allowed
        return function (ServerRequestInterface $request, ResponseInterface $response) use ($exception) {
            return $this->buildHttpResponse($request, $response, $exception);
        };
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptionDecorator(Exception $exception)
    {
        return function (ServerRequestInterface $request, ResponseInterface $response) use ($exception) {
            return $this->buildHttpResponse($request, $response, $exception);
        };
    }

    /**
     * {@inheritdoc}
     */
    protected function buildHttpResponse(ServerRequestInterface $request, ResponseInterface $response, Exception $exception)
    {
        $request = $request->withAttribute('route', $this->route);

        list($status, $message, $fullMessage) = $this->getStatusAndMessage($exception);

        if ($this->logger) {
            $this->logger->error($fullMessage, [$request->getMethod(), $request->getUri()]);
        }

        $eventResult = $this->event(get_class($exception), [$request, $response, $this->route]);
        if ($eventResult instanceof Response) {
            return $eventResult;
        }

        // Build response
        if ($this->isJson($request)) {
            return $this->buildJsonResponse($response, $status, $message);
        } else {
            if ($response->getBody()->isWritable()) {
                $response->getBody()->write($fullMessage);
            }
            return $response->withStatus($status, $message);
        }
    }

    /**
     * Get status code and message.
     *
     * @param Exception $exception
     * @return array
     */
    protected function getStatusAndMessage(Exception $exception)
    {
        if ($exception instanceof HttpException) {
            $message = $exception->getMessage();
            $status = $exception->getStatusCode();
        } else {
            $message = trim("Internal Server Error. " . $exception->getMessage());
            $status = 500;
        }
        $fullMessage = sprintf('Error %s: %s', $status, $message);

        return [$status, $message, $fullMessage];
    }

    /**
     * Returns true if a JSON request has been received.
     *
     * @param ServerRequestInterface $request Request
     * @return bool Status
     */
    protected function isJson(ServerRequestInterface $request)
    {
        $type = $request->getHeader('content-type');
        return !empty($type[0]) && (strpos($type[0], 'application/json') !== false);
    }

    /**
     * Build json response.
     *
     * @param ResponseInterface $response Response
     * @param int $code HTTP status code
     * @param string $message Message
     * @return ResponseInterface Response
     */
    protected function buildJsonResponse(ResponseInterface $response, $code, $message)
    {
        $response->getBody()->write(json_encode([
            'code' => $code,
            'message' => $message
        ]));

        $response = $response->withAddedHeader('content-type', 'application/json');
        return $response;
    }
}
