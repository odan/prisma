<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\User;
use App\Domain\User\UserService;
use Cake\Chronos\Chronos;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class HomeLoadAction implements ActionInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * Constructor.
     *
     * @param Auth $auth
     * @param UserService $userService
     */
    public function __construct(Auth $auth, UserService $userService)
    {
        $this->auth = $auth;
        $this->userService = $userService;
    }

    /**
     * Action (Json).
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface Json response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $userId = $this->auth->getUserId();
        $user = $this->userService->getUserById($userId);

        $result = [
            'message' => __('Loaded successfully!'),
            'now' => Chronos::now()->toDateTimeString(),
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ],
        ];

        return $response->withJson($result);
    }
}
