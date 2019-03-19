<?php

namespace App\Action;

use App\Domain\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Action.
 */
class UserIndexAction implements ActionInterface
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param Twig $twig
     * @param UserService $userService
     */
    public function __construct(Twig $twig, UserService $userService)
    {
        $this->twig = $twig;
        $this->userService = $userService;
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
        $viewData = [
            'users' => $this->userService->findAllUsers(),
        ];

        return $this->twig->render($response, 'User/user-index.twig', $viewData);
    }
}
