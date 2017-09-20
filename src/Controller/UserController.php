<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserRepository;
use App\Service\User\UserService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * UserController
 */
class UserController extends AbstractController
{
    /**
     * Index
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response The new response
     */
    public function indexPage(Request $request, Response $response): Response
    {
        $userRepository = new UserRepository($this->db);
        $users = $userRepository->findAll();

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
     * @return Response The new response
     */
    public function editPage(Request $request, Response $response): Response
    {
        // Get all GET parameters
        //$query = $request->getQueryParams();

        // Get all POST/JSON parameters
        //$post = $request->getParsedBody();

        // Get routing arguments
        $userId = $request->getAttribute('id');

        // Repository example
        $userRepository = new UserRepository($this->db);
        $user = $userRepository->getById($userId);

        // Insert a new user
        $newUser = new User();
        $newUser->username = 'admin-' . uuid();
        $newUser->disabled = 0;
        $newUserId = $userRepository->insert($newUser);

        // Get new new user
        $newUser = $userRepository->getById($newUserId);

        // Delete a user
        $userRepository->delete($newUser);

        // Get all users
        $users = $userRepository->findAll();

        // Session example
        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        // Logger example
        $this->logger->info('My log message');

        // Add data to template
        $viewData = $this->getViewData($request, [
            'id' => $user->id,
            'username' => $user->username,
            'counter' => $counter,
            'users' => $users
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
    public function reviewPage(Request $request, Response $response, array $args = null): Response
    {
        $id = $args['id'];
        $response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $response;
    }
}
