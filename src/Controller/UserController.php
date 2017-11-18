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
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Index
     *
     * @return Response The new response
     */
    public function indexPage(): Response
    {
        $users = $this->userRepository->findAll();

        $viewData = $this->getViewData([
            'users' => $users
        ]);

        return $this->render('view::User/user-index.html.php', $viewData);
    }

    /**
     * Edit page
     *
     * @param string $id The User ID (routing argument)
     * @return Response The new response
     */
    public function editPage(string $id): Response
    {
        // Get all GET parameters
        //$query = $request->getQueryParams();

        // Get all POST/JSON parameters
        //$post = $request->getParsedBody();

        // Repository example
        $user = $this->userRepository->getById($id);

        // Insert a new user
        $newUser = new User();
        $newUser->username = 'admin-' . uuid();
        $newUser->disabled = 0;
        $newUserId = $this->userRepository->insert($newUser);

        // Get new new user
        $newUser = $this->userRepository->getById($newUserId);

        // Delete a user
        $this->userRepository->delete($newUser);

        // Get all users
        $users = $this->userRepository->findAll();

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
    public function reviewPage(string $id): Response
    {
        $this->response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $this->response;
    }
}
