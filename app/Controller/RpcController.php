<?php

namespace App\Controller;

use Exception;
use ReflectionClass;
use ReflectionMethod;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

/**
 * RpcController
 */
class RpcController extends AppController
{

    /**
     * Action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request = null, Response $response = null)
    {
        $jsonRequest = array();

        try {
            $jsonRequest = $this->getJsonRequest($request);

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

            $request = $request->withAttribute('jsonrpc', $data);

            // Send json rpc response
            $response = $this->getJsonResponse($data);

            // Call controller action
            $response = $this->callFunction($object, $methodName, $request, $response, $jsonRequest);

            // $request = $request->withAttribute('json', $data);
        } catch (Exception $ex) {
            $response = $this->getJsonErrorResponse($ex, $jsonRequest, $request, $response);
        }
        return $response;
    }

    /**
     * Send value as json string
     *
     * @param array $data
     * @return $response
     */
    protected function getJsonResponse($data)
    {
        $response = new JsonResponse($data);
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
    protected function getJsonRequest(Request $request)
    {
        if (!$this->isJsonRpc($request)) {
            throw new Exception('Invalid Json-RPC request');
        }
        $requestContent = $request->getBody()->__toString();
        $result = json_decode($requestContent, true);

        if (empty($result) || !is_array($result)) {
            throw new Exception('Invalid Json-RPC request');
        }
        return $result;
    }

    /**
     * Returns true if a JSON-RCP request has been received
     * @return boolean
     */
    protected function isJsonRpc(Request $request)
    {
        $method = $request->getMethod();
        $contentType = $request->getHeaderLine('content-type');

        return $method === 'POST' && !empty($contentType) &&
                (strpos($contentType, 'application/json') !== false);
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
    protected function getJsonErrorResponse(Exception $ex, $jsonRequest)
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
}
