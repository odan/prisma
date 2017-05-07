<?php

namespace App\Middleware;

use Exception;
use RuntimeException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * FastRouteMiddleware
 */
class FastRouteMiddleware
{

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * @var Dispatcher FastRoute dispatcher
     */
    private $router;

    /**
     * Set the Dispatcher instance.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $default = [
            'routes' => [],
            'events' => [],
            'dispatcher' => 'FastRoute\simpleDispatcher'
        ];
        $this->options = $options + $default;

        $dispatcher = $this->options['dispatcher'];
        $routes = $this->options['routes'];
        $this->router = $dispatcher(function (RouteCollector $r) use ($routes) {
            foreach ($routes as $route) {
                // $httpMethod, $route, $handler
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });
    }

    /**
     * Wrap the remaining middleware with error handling.
     *
     * @param Request $request The request.
     * @param Response $response The response.
     * @param callable $next Callback to invoke the next middleware.
     * @return Response A response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $uri = $this->getBaseUri($request);
        $route = $this->router->dispatch($request->getMethod(), $uri);
        if ($route[0] === Dispatcher::NOT_FOUND) {
            $stream = new Stream('php://temp', 'wb+');
            $stream->write('Not found');
            return $response->withStatus(404)->withBody($stream);
        }
        if ($route[0] === Dispatcher::METHOD_NOT_ALLOWED) {
            $stream = new Stream('php://temp', 'wb+');
            $stream->write('Not allowed');
            return $response->withStatus(405)->withBody($stream);
        }
        $request = $request->withAttribute('vars', $route[2]);
        $response = $this->executeCallable($route[1], $request, $response, $next);
        return $next($request, $response);
    }

    /**
     * Returns the url path leading up to the current script.
     * Used to make the webapp portable to other locations.
     *
     * @return string uri
     */
    public function getBaseUri(Request $request)
    {
        // Get URI from URL
        $uri = $request->getUri()->getPath();

        // Detect and remove subfolder from URI
        $server = $request->getServerParams();
        $scriptName = $server['SCRIPT_NAME'];

        if (isset($scriptName)) {
            $dirname = dirname($scriptName);
            $dirname = dirname($dirname);
            $len = strlen($dirname);
            if ($len > 0 && $dirname != '/') {
                $uri = substr($uri, $len);
            }
        }
        return $uri;
    }

    /**
     * Execute the callable.
     *
     * @param mixed $target
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    private function executeCallable($target, Request $request, Response $response, callable $next)
    {
        ob_start();
        $level = ob_get_level();
        try {
            $callback = $this->getCallable($target);

            // Event handler
            $eventParams = [$request, $response, $target, $callback];
            $eventResult = $this->triggerBeforeAction($eventParams);
            if ($eventResult instanceof Response) {
                return $eventResult;
            }
            if (is_array($callback)) {
                list($class, $method) = $callback;
                $callback = new $class();
                if ($method === '__invoke') {
                    // Call middleware
                    $return = $callback->{$method}($request, $response, $next);
                } else {
                    $return = $callback->{$method}($request, $response);
                }
            } else {
                $return = call_user_func_array($callback, [$request, $response]);
            }

            if ($return instanceof Response) {
                $response = $return;
                $return = '';
            }
            $return = $this->getOutput($level) . $return;
            $body = $response->getBody();
            if ($return !== '' && $body->isWritable()) {
                $body->write($return);
            }
            return $response;
        } catch (Exception $exception) {
            $this->getOutput($level);
            throw $exception;
        }
    }

    /**
     * Trigger event
     *
     * @param array $params
     * @return mixed
     */
    protected function triggerBeforeAction($params)
    {
        $name = 'before.action';
        if (!isset($this->options['events'][$name])) {
            return true;
        }
        $eventCallback = $this->getCallable($this->options['events'][$name]);
        return call_user_func_array($eventCallback, $params);
    }

    /**
     * Resolves the target of the route and returns a callable.
     *
     * @param mixed $target
     *
     * @throws RuntimeException If the target is not callable
     *
     * @return callable
     */
    protected function getCallable($target)
    {
        if (empty($target)) {
            throw new RuntimeException('No callable provided');
        }
        if (is_string($target)) {
            if (strpos($target, '->') !== false) {
                return explode('->', $target, 2);
            }
        }
        // If it's callable as is
        if (is_callable($target)) {
            return $target;
        }
        throw new RuntimeException('Invalid callable provided');
    }

    /**
     * Return the output buffer.
     *
     * @param int $level
     *
     * @return string
     */
    public static function getOutput($level)
    {
        $output = '';
        while (ob_get_level() >= $level) {
            $output .= ob_get_clean();
        }
        return $output;
    }
}
