<?php

namespace App\Util;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

/**
 * PSR-7 JsonRpcServer
 */
class JsonServer
{

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
     * Constructor
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request = null, Response $response = null)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Run handler
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function run()
    {
        $jsonRequest = array();
        $request = $this->request;
        $response = $this->response;

        try {
            $jsonRequest = $this->getJsonRequest();

            // Get controller object
            list($className, $methodName) = explode('.', $jsonRequest['method']);
            $controllerName = sprintf('\App\Controller\%sController', $className);
            $object = $this->getObject($controllerName, $methodName);

            // Create response with result
            $data = array(
                'jsonrpc' => '2.0',
                'id' => value($jsonRequest, 'id', null),
                'result' => null
            );

            // Send json rpc response
            $response = $this->getJsonResponse($data);

            // Call controller action
            $response = $this->callFunction($object, $methodName, $request, $response, $jsonRequest);
        } catch (Exception $ex) {
            $response = $this->getResponseByException($ex, $jsonRequest, $request, $response);
        }
        return $response;
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
            $result = $object->{$methodName}($request, $response, $jsonRequest['params']);
        } else {
            $result = $object->{$methodName}($request, $response);
        }
        return $result;
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
    public function isJsonRpc()
    {
       $method = $this->request->getMethod();
        $type = $this->request->getHeader('content-type');
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
        $object = new $className();
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
