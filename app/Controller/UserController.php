<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * UserController
 */
class UserController extends AppController
{

    /**
     * Index
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request = null, Response $response = null)
    {
        // Append content to response
        $response->getBody()->write("User index action<br>");
        return $response;
    }

    /**
     * Edit
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
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
        $vars = $request->getAttribute('vars');
        $id = $vars['id'];

        // Get config value
        $app = $this->app($request);
        $env = $app->config['env']['name'];

        // Get GET parameter
        $id = $app->http->get('id');

        // Increment counter
        $counter = $app->session->get('counter', 0);
        $counter++;
        $app->session->set('counter', $counter);

        $app->logger->info('My log message');

        // Set locale
        //$app->session->set('user.locale', 'de_DE');
        //
        //Model example
        //$user = new \App\Model\User($app);
        //$userRows = $user->getAll();
        //$userRow = $user->getById($id);
        //
        // Add data to template
        $data = $this->getData($request, [
            'id' => $id,
            'assets' => $this->getAssets(),
            'content' => 'view::User/html/edit.html.php'
        ]);

        // Render template
        $content = $app->view->render('view::Layout/html/layout.html.php', $data);

        // Return new response
        $response->getBody()->write($content);
        return $response;
    }
}


