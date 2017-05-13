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
     * @return Response
     */
    public function indexPage()
    {
        // Render template
        $request = $this->getRequest();
        $viewData = $this->getViewData($this->getRequest());
        $content = view()->render('view::User/user-index.html.php', $viewData);

        $response = $this->getResponse();
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * Edit
     *
     * @param array $args Arguments
     * @return Response Response
     */
    public function editPage(array $args)
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

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
        //$attributes = $request->getAttributes();
        //$vars = $request->getAttribute('vars');
        $id = $args['id'];

        // Get config value
        $env = config()->get('env');

        // Get GET parameter
        //$id = $queryParams['id'];

        // Increment counter
        $session = session();
        $counter = $session->get('counter', 0);
        $counter++;
        $session->set('counter', $counter);

        logger()->info('My log message');

        // Set locale
        //$app->session->set('user.locale', 'de_DE');
        //
        //Model example
        //$user = new \App\Model\User($app);
        //$userRows = $user->getAll();
        //$userRow = $user->getById($id);
        //
        // Add data to template
        $viewData = $this->getViewData($request, [
            'id' => $id,
            'counter' => $counter,
            'assets' => $this->getAssets(),
        ]);

        // Render template
        $content = view()->render('view::User/user-edit.html.php', $viewData);

        // Return new response
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * Test page.
     *
     * @param array $args Arguments
     * @return Response Response
     */
    public function reviewPage(array $args)
    {
        $id = $args['id'];

        $response = $this->getResponse();
        $response->getBody()->write("Action: Show all reviews of user: $id<br>");

        /// Uncomment this line to test the ExceptionMiddleware
        //throw new \Exception('My error', 1234);

        return $response;
    }
}
