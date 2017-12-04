<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * LoginController.
 */
class LoginController extends AbstractController
{
    /**
     * User login
     *
     * @param Response $response
     * @return ResponseInterface
     */
    public function loginAction(Response $response): ResponseInterface
    {
        $this->user->logout();
        $viewData = $this->getViewData();
        return $this->render($response, 'Login/login.twig', $viewData);
    }

    /**
     * User login submit
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function loginSubmitAction(Request $request, Response $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $result = $this->user->login($username, $password);
        $url = ($result) ? $this->router->pathFor('root') : $this->router->pathFor('login');

        return $response->withRedirect($url);
    }

    /**
     * User logout
     *
     * @param Response $response
     * @return ResponseInterface Redirect response
     */
    public function logoutAction(Response $response): ResponseInterface
    {
        $this->user->logout();
        return $response->withRedirect($this->router->pathFor('login'));
    }
}
