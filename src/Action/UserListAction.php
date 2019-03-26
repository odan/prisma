<?php

namespace App\Action;

use App\Domain\User\UserList;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class UserListAction implements ActionInterface
{
    /**
     * @var UserList
     */
    protected $service;

    /**
     * Constructor.
     *
     * @param UserList $service the service
     */
    public function __construct(UserList $service)
    {
        $this->service = $service;
    }

    /**
     * Action.
     *
     * @param Request $request the request
     * @param Response $response the response
     *
     * @return ResponseInterface the response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $params = $request->getParsedBody();
        $result = $this->service->listAllUsers($params);

        return $response->withJson($result);
    }
}
