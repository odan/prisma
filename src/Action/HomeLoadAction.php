<?php

namespace App\Action;

use App\Repository\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * HomeLoadAction.
 */
class HomeLoadAction extends AbstractAction
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
     * Action (Json).
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface Json response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $userId = $this->auth->getId();
        $user = $this->userRepository->getById($userId);

        $result = [
            'message' => __('Loaded successfully!'),
            'now' => now(),
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
            ],
        ];

        return $response->withJson($result);
    }
}
