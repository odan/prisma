<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class UserLoginLogoutAction extends BaseAction
{
    /**
     * User logout.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface Redirect response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $this->auth->logout();

        return $response->withRedirect($this->router->pathFor('login'));
    }
}
