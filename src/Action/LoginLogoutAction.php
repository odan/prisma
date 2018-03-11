<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * LoginLogoutAction.
 */
class LoginLogoutAction extends AbstractAction
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
        $this->auth->clearIdentity();

        return $response->withRedirect($this->router->pathFor('login'));
    }
}
