<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\User\UserRepository;
use Exception;
use Psr\Http\Message\ResponseInterface;
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
     * @param Response $response
     * @return ResponseInterface The new response
     */
    public function indexAction(Response $response): ResponseInterface
    {
        $users = $this->userRepository->findAll();

        $viewData = $this->getViewData([
            'users' => $users
        ]);

        return $this->render($response, 'User/user-index.twig', $viewData);
    }

    /**
     * Edit page
     *
     * @param Response $response
     * @param string $id
     * @return ResponseInterface The new response
     * @throws Exception
     */
    public function editAction(Response $response, string $id): ResponseInterface
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
        return $this->render($response, 'User/user-edit.twig', $viewData);
    }

    /**
     * User review page.
     *
     * @param Response $response
     * @param string $id
     * @return Response Response
     */
    public function reviewAction(Response $response, string $id): ResponseInterface
    {
        // $id = $args['id'];

        $response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $response;
    }
}
