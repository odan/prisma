<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * LoginController.
 */
class LoginController extends AppController
{
    /**
     * User login
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response
     */
    public function loginPage(Request $request, Response $response): Response
    {
        $this->user->logout();
        $viewData = $this->getViewData($request);
        return $this->render($response, 'view::Login/login.html.php', $viewData);
    }

    /**
     * User login submit
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response
     */
    public function loginSubmit(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $result = $this->user->login($username, $password);
        $url = ($result) ? '/' : '/login';

        return $this->redirect($request, $response, $url);
    }

    /**
     * User logout
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Redirect response
     */
    public function logout(Request $request, Response $response): Response
    {
        $this->user->logout();
        return $this->redirect($request, $response, '/login');
    }
}
