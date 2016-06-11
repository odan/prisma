<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController extends AppController
{

    public function index(Request $request = null, Response $response = null)
    {
        // Append content to response
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    public function edit(Request $request = null, Response $response = null)
    {
        // Get query parameter
        $id = $request->getAttribute('id');

        // Get config value
        $app = $this->container($request);
        $env = $app->config['env']['name'];

        // Add data to template
        $data = [
            'id' => $id,
            'env' => $env
        ];

        $app->logger->info('My log message');

        // Render template
        $content = $app->view->render('view::Index/html/index.html.php', $data);

        // Return new response
        $response = $response->getBody()->write($content);
        return $response;
    }

}


