<?php

namespace App\Action;

use App\Domain\User\Auth;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Action.
 */
class UserLogoutAction implements ActionInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param Auth $auth
     */
    public function __construct(Router $router, Auth $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

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
