<?php

namespace App\Controller;

use Slim\Http\Response;

/**
 * LoginController.
 */
class LoginController extends AbstractController
{
    /**
     * User login
     *
     * @return Response
     */
    public function loginPage(): Response
    {
        $this->user->logout();
        $viewData = $this->getViewData();
        return $this->render('Login/login.twig', $viewData);
    }

    /**
     * User login submit
     *
     * @return Response
     */
    public function loginSubmit(): Response
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
     * @return Response Redirect response
     */
    public function logout(): Response
    {
        $this->user->logout();
        return $this->response->withRedirect($this->router->pathFor('login'));
    }
}
