<?php

namespace App\Action;

use App\Domain\User\UserService;
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
     * @return ResponseInterface The new response
     */
    public function __invoke(Request $request, Response $response, array $args = []): ResponseInterface
    {
        $userId = (int)$args['id'];

        $user = $this->userService->getUserById($userId);

        $viewData = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
        ];

        return $this->twig->render($response, 'User/user-edit.twig', $viewData);
    }
}
