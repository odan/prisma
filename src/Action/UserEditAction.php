<?php

namespace App\Action;

use App\Domain\User\User;
use App\Domain\User\UserService;
use Exception;
use Odan\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Action.
 */
class UserEditAction implements ActionInterface
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param Twig $twig
     * @param SessionInterface $session
     * @param LoggerInterface $logger
     * @param UserService $userService
     */
    public function __construct(Twig $twig, SessionInterface $session, LoggerInterface $logger, UserService $userService)
    {
        $this->twig = $twig;
        $this->session = $session;
        $this->logger = $logger;
        $this->userService = $userService;
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
        $newUser = new User();
        $newUser->setUsername('admin-' . uuid());
        $newUser->setEnabled(true);
        $this->userService->registerUser($newUser);

        // Get new user
        //$newUser = $this->userService->getUserById($userId);

        // Session example
        // Increment counter
        $counter = $this->session->get('counter') ?? 0;
        $this->session->set('counter', $counter++);

        // Logger example
        $this->logger->info('My log message');

        $viewData = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'counter' => $counter,
        ];

        // Render template
        return $this->twig->render($response, 'User/user-edit.twig', $viewData);
    }
}
