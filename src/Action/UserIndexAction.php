<?php

namespace App\Action;

use App\Domain\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class UserIndexAction extends AbstractAction
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param Container $container The container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userService = $container->get(UserService::class);
    }

    /**
     * Index.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface The new response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $users = $this->userService->findAllUsers();

        $viewData = $this->getViewData([
            'users' => $users,
        ]);

        return $this->render($response, 'User/user-index.twig', $viewData);
    }
}
