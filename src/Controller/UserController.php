<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController
{

    public function index(Request $request = null, Response $response = null)
    {
        // Append content to response
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    public function edit(Request $request = null, Response $response = null)
    {
        // Simple echo is also possible.
        // The middleware will catch it and convert it to a response object.
        $id = $request->getAttribute('id');
        echo "Edit user with ID: $id<br>";
        //return $response;
    }

}


