<?php

namespace App\Controller;

use App\Container\App;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * IndexController
 */
class IndexController extends AppController
{

    /**
     * Index action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request = null, Response $response = null)
    {
        $body = $response->getBody();
        $body->write("Hello world<br>Default index page<br><br>");
        $body->write('Testlink 1: <a href="users">users/</a><br>');
        $body->write('Testlink 2: <a href="user/1234">users/1234</a><br>');
        return $response;
    }

}
