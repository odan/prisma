<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * JSON-RPC controller
 */
class JsonController extends AppController
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
        $server = new \App\Util\JsonServer($request, $response);
        return $server->run();
    }
}
