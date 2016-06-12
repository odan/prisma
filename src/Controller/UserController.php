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
        // All GET parameters
        $queryParams = $request->getQueryParams();

        // All POST or PUT parameters
        $postParams = $request->getParsedBody();

        // Single GET parameter
        //$title = $queryParams['title'];
        //
        // Single POST/PUT parameter
        //$data = $postParams['data'];
        //
        // Get routing arguments
        $args = $request->getAttribute('args');
        $id = $args['id'];

        // Get config value
        $app = $this->app($request);
        $env = $app->config['env']['name'];

        // Increment counter
        $counter = $app->session->get('counter', 0);
        $counter++;
        $app->session->set('counter', $counter);

        // Add data to template
        $data = [
            'id' => $id,
            'env' => $env,
            'counter' => $counter
        ];

        $app->logger->info('My log message');

        // Render template
        $content = $app->view->render('view::Index/html/index.html.php', $data);

        // Return new response
        $response->getBody()->write($content);
        return $response;
    }

}


