<?php

namespace App\Controller;

use App\Table\UserTable;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * UserController
 */
class UserController extends AppController
{
    /**
     * Index
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response
     */
    public function indexPage(Request $request, Response $response)
    {
        $userRepo = new UserTable($this->db);
        $users = $userRepo->getAll();

        $viewData = $this->getViewData($request, [
            'users' => $users
        ]);

        return $this->render($response, 'view::User/user-index.html.php', $viewData);
    }

    /**
     * Edit page
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response
     */
    public function editPage(Request $request, Response $response)
    {
        // Get all GET parameters
        //$query = $request->getQueryParams();

        // Get all POST/JSON parameters
        //$post = $request->getParsedBody();

        // Get routing arguments
        $userId = $request->getAttribute('id');

        // Repository example
        $userRepo = new UserTable($this->db);
        $user = $userRepo->findById($userId);

        // Session example
        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        // Logger example
        $this->logger->info('My log message');

        // Add data to template
        $viewData = $this->getViewData($request, [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'counter' => $counter,
            'assets' => $this->getAssets(),
        ]);

        // Render template
        return $this->render($response, 'view::User/user-edit.html.php', $viewData);
    }

    /**
     * User review page.
     *
     * @param Request $request The request
     * @param Response $response The response
     * @param array|null $args Arguments
     * @return Response Response
     */
    public function reviewPage(Request $request, Response $response, array $args = null)
    {
        $id = $args['id'];
        $response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $response;
    }
}
