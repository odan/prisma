<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;

/**
 * LoginController.
 */
class LoginController extends AbstractController
{
    /**
     * User login
     *
     * @return ResponseInterface
     */
    public function loginPage(): ResponseInterface
    {
        $this->user->logout();
        $viewData = $this->getViewData();
        return $this->render('Login/login.twig', $viewData);
    }

    /**
     * User login submit
     *
     * @return ResponseInterface
     */
    public function loginSubmit(): ResponseInterface
    {
        $data = $this->request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $result = $this->user->login($username, $password);
        $url = ($result) ? $this->router->pathFor('root') : $this->router->pathFor('login');

        return $this->response->withRedirect($url);
    }

    /**
     * User logout
     *
     * @return ResponseInterface Redirect response
     */
    public function logout(): ResponseInterface
    {
        $this->user->logout();
        return $this->response->withRedirect($this->router->pathFor('login'));
    }
}
