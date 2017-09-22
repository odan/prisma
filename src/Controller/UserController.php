<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserRepository;
use Slim\Http\Response;

/**
 * UserController
 */
class UserController extends AbstractController
{
    /**
     * Index
     *
     * @param UserRepository $userRepository The User repository
     * @return Response The new response
     */
    public function indexPage(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        $viewData = $this->getViewData([
            'users' => $users
        ]);

        return $this->render('view::User/user-index.html.php', $viewData);
    }

    /**
     * Edit page
     *
     * @param string $id The User ID (routing argument)
     * @param UserRepository $userRepository The User repository
     * @return Response The new response
     */
    public function editPage($id, UserRepository $userRepository): Response
    {
        // Get all GET parameters
        //$query = $request->getQueryParams();

        // Get all POST/JSON parameters
        //$post = $request->getParsedBody();

        // Repository example
        $user = $userRepository->getById($id);

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
        $viewData = $this->getViewData([
            'id' => $user->id,
            'username' => $user->username,
            'counter' => $counter,
            'users' => $users
        ]);

        // Render template
        return $this->render('view::User/user-edit.html.php', $viewData);
    }

    /**
     * User review page.
     *
     * @param string $id
     * @return Response Response
     */
    public function reviewPage($id): Response
    {
        $this->response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $this->response;
    }
}
