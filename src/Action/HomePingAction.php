<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class HomePingAction implements ActionInterface
{
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
        return $response->withJson($request->getParsedBody());
    }
}
