<?php

namespace App\Controller;

use App\Service\User\UserSession;
use Cake\Datasource\ConnectionInterface;
use League\Plates\Engine;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest as Request;

/**
 * UserController
 */
class UserController extends AppController
{

    /**
     * @var ConnectionInterface
     */
    protected $db;

    /**
     * @var Engine
     */
    protected $view;

    /**
     * @var UserSession
     */
    protected $user;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * UserController constructor.
     *
     * @param Request $request
     * @param Response $response
     * @param ConnectionInterface $db
     * @param Engine $view
     * @param UserSession $user
     * @param LoggerInterface $logger
     */
    public function __construct(Request $request,
                                Response $response,
                                ConnectionInterface $db,
                                Engine $view,
                                UserSession $user,
                                LoggerInterface $logger)
    {
        parent::__construct($request, $response);

        $this->db = $db;
        $this->view = $view;
        $this->user = $user;
        $this->logger = $logger;
    }

    /**
     * Index
     *
     * @return Response
     */
    public function indexPage()
    {
        $viewData = $this->getViewData();
        return $this->render('view::User/user-index.html.php', $viewData);
    }

    /**
     * Edit
     *
     * @param array $args Arguments
     * @return Response Response
     */
    public function editPage(array $args)
    {
        //$request = $this->getRequest();
        //$response = $this->getResponse();

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
        $id = $args['id'];

        // Get config value
        //$env = config()->get('env');

        // Get GET parameter
        //$id = $queryParams['id'];

        // Increment counter
        $user = user();
        $counter = $user->get('counter', 0);
        $counter++;
        $user->set('counter', $counter);

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
