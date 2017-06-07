<?php

namespace App\Controller;

use App\Service\User\UserSession;
use Cake\Database\Connection;
use League\Plates\Engine;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * UserController
 */
class UserController extends AppController
{

    /**
     * Constructor.
     *
     * @param Engine $view
     * @param Connection $db
     * @param LoggerInterface $logger
     * @param UserSession $user
     */
    public function __construct(Engine $view, Connection $db, UserSession $user, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->db = $db;
        $this->user = $user;
        $this->logger = $logger;
    }

    /**
     * Index
     *
     * @param Request $request The request
     * @return Response
     */
    public function indexPage(Request $request)
    {
        $this->setRequest($request);
        $viewData = $this->getViewData();
        return $this->render('view::User/user-index.html.php', $viewData);
    }

    /**
     * Edit page
     *
     * @param Request $request The request
     * @return Response
     */
    public function editPage(Request $request)
    {
        $this->setRequest($request);

        //$request = request();
        //$response = response();

        // All GET parameters
        //$queryParams = $request->getQueryParams();

        // All POST or PUT parameters
        //$postParams = $request->getParsedBody();

        // Single GET parameter
        //$title = $queryParams['title'];
        //
        // Single POST/PUT parameter
        //$data = $postParams['data'];
        //
        // Get routing arguments
        //$attributes = $request->getAttributes();
        //$vars = $request->getAttribute('vars');
        $id = $request->getAttribute('id');

        // Get config value
        //$env = config()->get('env');

        // Get GET parameter
        //$id = $queryParams['id'];

        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        $this->logger->info('My log message');

        // Set locale
        //$app->session->set('user.locale', 'de_DE');
        //
        //Model example
        //$user = new \App\Model\User($app);
        //$userRows = $user->getAll();
        //$userRow = $user->getById($id);
        //
        // Add data to template
        $viewData = $this->getViewData([
            'id' => $id,
            'counter' => $counter,
            'assets' => $this->getAssets(),
        ]);

        // Render template
        return $this->render('view::User/user-edit.html.php', $viewData);
    }

    /**
     * Test page.
     *
     * @param Request $request The request
     * @param Response $response The response
     * @param array|null $args Arguments
     * @return Response Response
     */
    public function reviewPage(Request $request, Response $response, array $args = null)
    {
        $this->setRequest($request);
        $id = $args['id'];
        $response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $response;
    }
}
