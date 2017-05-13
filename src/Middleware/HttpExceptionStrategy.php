<?php

namespace App\Middleware;

use Exception;
use League\Route\Http\Exception\MethodNotAllowedException;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception as HttpException;
use League\Route\Route;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use League\Route\Strategy\StrategyInterface;
use Zend\Diactoros\Response;

class HttpExceptionStrategy implements StrategyInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    protected $route = null;

    protected $events = array();

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function on($event, callable $callback)
    {
        $this->events[$event] = $callback;
    }

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

        if ($exception instanceof HttpException) {
            $message = $exception->getMessage();
            $status = $exception->getStatusCode();
        } else {
            $message = trim("Internal Server Error. " . $exception->getMessage());
            $status = 500;
        }
        $fullMessage = sprintf('Error %s: %s', $status, $message);

        if ($this->logger) {
            $this->logger->error($fullMessage, [$request->getMethod(), $request->getUri()]);
        }

        $eventResult = $this->event(get_class($exception), [$request, $response, $this->route]);
        if ($eventResult instanceof Response) {
            return $eventResult;
        }

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
     * Returns true if a JSON-RCP request has been received.
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
     * @param ResponseInterface $response
     * @param int $code
     * @param string $message
     * @return ResponseInterface
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
