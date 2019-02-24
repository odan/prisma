<?php

namespace App\Action;

use App\Domain\User\UserData;
use App\Domain\User\UserService;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class UserEditAction extends BaseAction
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userService = $this->factory->create(UserService::class);
    }

    /**
     * Edit page.
     *
     * @param Request $request The request
     * @param Response $response The response
     * @param mixed[] $args Arguments
     *
     * @throws Exception
     *
     * @return ResponseInterface The new response
     */
    public function __invoke(Request $request, Response $response, array $args): ResponseInterface
    {
        $id = (int)$args['id'];

        // Get all GET parameters
        //$query = $request->getQueryParams();

        // Get all POST/JSON parameters
        //$post = $request->getParsedBody();

        // Repository example
        $user = $this->userService->getUserById($id);

        // Insert a new user
        $newUser = new UserData();
        $newUser->setUsername('admin-' . uuid());
        $newUser->setEnabled(true);
        $this->userService->registerUser($newUser);

        // Get new user
        //$newUser = $this->userService->getUserById($userId);

        // Session example
        // Increment counter
        $counter = $this->session->get('counter', 0);
        $this->session->set('counter', $counter++);

        // Logger example
        $this->logger->info('My log message');

        $viewData = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'counter' => $counter,
        ];

        // Render template
        return $this->render($response, 'User/user-edit.twig', $viewData);
    }
}
