<?php

namespace App\Action;

use App\Domain\User\UserService;
use Cake\Chronos\Chronos;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class HomeLoadAction extends BaseAction
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
        $this->userService = $this->factory->create(UserService::class);
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
