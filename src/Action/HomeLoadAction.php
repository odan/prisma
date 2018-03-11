<?php

namespace App\Action;

use App\Table\UserTable;
use Interop\Container\Exception\ContainerException;
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
     * @var UserTable
     */
    protected $userTable;

    /**
     * Constructor.
     *
     * @param Container $container
     *
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userTable = $container->get(UserTable::class);
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
        $user = $this->userTable->getById($userId);

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
