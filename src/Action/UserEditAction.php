<?php

namespace App\Action;

use App\Model\UserModel;
use App\Repository\UserRepository;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Webmozart\Assert\Assert;

/**
 * UserEditAction.
 */
class UserEditAction extends AbstractAction
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userRepository = $container->get(UserRepository::class);
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
        $id = $args['id'];

        // Get all GET parameters
        //$query = $request->getQueryParams();

        // Get all POST/JSON parameters
        //$post = $request->getParsedBody();

        // Repository example
        $user = $this->userRepository->getById($id);

        // Insert a new user
        $newUser = new UserModel();
        $newUser->setUsername('admin-' . uuid());
        $newUser->setDisabled(0);
        $newUserId = $this->userRepository->insertUser($newUser);

        // Get new new user
        $newUser = $this->userRepository->getById($newUserId);

        assert($newUser->getId() !== null);
        $this->userRepository->deleteUser($newUser->getId());

        // Get all users
        $users = $this->userRepository->findAll();

        // Session example
        // Increment counter
        $counter = $this->session->get('counter', 0);
        $counter++;
        $this->session->set('counter', $counter);

        // Logger example
        $this->logger->info('My log message');

        // Add data to template
        $viewData = $this->getViewData([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'counter' => $counter,
            'users' => $users,
        ]);

        // Render template
        return $this->render($response, 'User/user-edit.twig', $viewData);
    }
}
