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

    public function edit(Request $request = null, Response $response = null, $vars = null)
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

        // Set locale
        //$app->session->set('locale', 'de_DE');
        //
        //Model example
        //$user = new \App\Model\User($app);
        //$userRows = $user->getAll();
        //$userRow = $user->getById($id);
        //
        // Add data to template
        $assets = $this->getAssets();
        $jsText = $this->getJsText($this->getTextAssets());

        $data = [
            'baseurl' => $request->getAttribute('base_url'),
            'assets' => $assets,
            'jstext' => $jsText,
            'content' => 'view::User/html/edit.html.php',
            'id' => $id,
        ];

        $app->logger->info('My log message');

        // Render template
        $content = $app->view->render('view::Layout/html/layout.html.php', $data);

        // Return new response
        $response->getBody()->write($content);
        return $response;
    }
}


