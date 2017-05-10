<?php

/**
 * JSON-RPC 2.0 middleware
 *
 */

namespace App\Middleware;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

/**
 * JSONRPC 2.0 Middleware
 */
class JsonRpcMiddleware
{
    /**
     * Attribute
     *
     * @var string Attribute
     */
    const ATTRIBUTE = 'json';

    /**
     * Options
     *
     * @var array
     */
    protected $options;

    /**
     * Request
     *
     * @var Request
     */
    protected $request;

    /**
     * Response
     *
     * @var Request
     */
    protected $response;

    /**
     * Set the Middleware instance and options.
     *
     * @param array $options
     * - class Classname to invoke
     */
    public function __construct($options = array())
    {
        $this->options = array_replace_recursive([
            'class' => '\App\Controller\%sController'
        ], $options);
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
        if (!$this->isJsonRpc($request)) {
            return $next($request, $response);
        }
        $this->request = $request;
        $this->response = $response;
        $response = $this->run();
        return $next($request, $response);
    }

     /**
     * Run handler
     *
     * @return Response
     */
    public function run()
    {
        $jsonRequest = array();
        $request = $this->request;
        $response = $this->response;

        try {
            $jsonRequest = $this->getJsonRequest();

            // Add json request
            $request = $request->withAttribute(static::ATTRIBUTE, $jsonRequest);

            // Get controller object
            list($className, $methodName) = explode('.', $jsonRequest['method']);
            $controllerName = sprintf($this->options['class'], $className);
            $object = $this->getObject($controllerName, $methodName);

            // Call controller action
            $result = $this->callFunction($object, $methodName, $request, $response, $jsonRequest);

            // Create response with result
            if (!($result instanceof Response)) {
                $response = $this->getJsonResponse(array(
                    'jsonrpc' => '2.0',
                    'id' => value($jsonRequest, 'id', null),
                    'result' => $result
                ));
            }
        } catch (Exception $ex) {
            $response = $this->getResponseByException($ex, $jsonRequest, $request, $response);
        }
        return $response;
    }

    /**
     * Call function ob object with paramters from json request
     *
     * @param mixed $object
     * @param string $methodName
     * @param array $jsonRequest
     * @return Response
     */
    protected function callFunction($object, $methodName, $request, $response, $jsonRequest)
    {
        if (isset($jsonRequest['params'])) {
            return $object->{$methodName}($request, $response, $jsonRequest['params']);
        } else {
            return $object->{$methodName}($request, $response);
        }
    }

    /**
     * Send value as json string
     *
     * @param array $data
     * @return $response
     */
    public function getJsonResponse($data, $status = 200)
    {
        $response = new JsonResponse($data, $status);
        $response = $response->withHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response = $response->withHeader('Pragma', 'no-cache');
        $response = $response->withHeader('Expires', '0');
        return $response;
    }

    /**
     * Get JSON request as array
     *
     * @return array
     * @throws Exception
     */
    protected function getJsonRequest()
    {
        if (!$this->isJsonRpc($this->request)) {
            throw new Exception('Invalid Json-RPC request');
        }
        $requestContent = $this->request->getBody()->__toString();
        $result = json_decode($requestContent, true);

        if (empty($result) || !is_array($result)) {
            throw new Exception('Invalid Json-RPC request');
        }
        return $result;
    }

    /**
     * Returns true if a JSON-RCP request has been received.
     *
     * @return boolean
     */
    public function isJsonRpc(Request $request)
    {
        $method = $request->getMethod();
        $type = $request->getHeader('content-type');
        return $method === 'POST' && !empty($type[0]) &&
                (strpos($type[0], 'application/json') !== false);
    }

    /**
     * Check request class method
     *
     * @param string $className
     * @param string $methodName
     * @throws Exception
     * @return mixed
     */
    protected function getObject($className, $methodName)
    {
        if (!class_exists($className)) {
            throw new Exception("Class '$methodName' not found");
        }
        $object = new $className($this->request, $this->response);
        $class = new ReflectionClass($object);
        if (!$class->hasMethod($methodName)) {
            throw new Exception("Method '$methodName' not found");
        }

        // check if method is public
        $method = new ReflectionMethod($object, $methodName);
        if (!$method->isPublic()) {
            throw new Exception("Action '$methodName' is not public");
        }
        return $object;
    }

    /**
     * Create json-rpc error response
     *
     * @param Exception $ex
     * @param array $jsonRequest
     * @return Response Response
     */
    protected function getResponseByException(Exception $ex, $jsonRequest)
    {
        $data = array(
            'jsonrpc' => '2.0',
            'id' => value($jsonRequest, 'id', 0),
            'error' => array(
                'code' => $ex->getCode(),
                'message' => $ex->getMessage()
            )
        );
        return $this->getJsonResponse($data);
    }

    /**
     * Create json-rpc error response
     *
     * @param Exception $ex
     * @param array $jsonRequest
     * @return Response Response
     */
    public function getResponseByError($message, $code = 0, $id = 0, $httpCode = 200)
    {
        $data = array(
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => array(
                'code' => $code,
                'message' => $message
            )
        );
        return $this->getJsonResponse($data, $httpCode);
    }
}
