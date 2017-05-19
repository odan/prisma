<?php

namespace App\Controller;

use Zend\Diactoros\Response;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * LoginController.
 */
class LoginController extends AppController
{
    /**
     * User login
     *
     * @return Response
     */
    public function loginPage()
    {
        user()->logout();

        $assets = $this->getAssets();
        $assets[] = 'view::Login/login.css';

        $view = view();
        $viewData = $this->getViewData();
        $content = $view->render('view::Login/login.html.php', $viewData);

        $response = response();
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * User login submit
     *
     * @return Response
     */
    public function loginSubmit()
    {
        $request = request();

        $data = $request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $result = user()->login($username, $password);
        $url = ($result) ? baseurl('/') : baseurl('/login');

        return new RedirectResponse($url);
    }

    /**
     * User logout
     *
     * @return Response
     */
    public function logout()
    {
        user()->logout();
        return new RedirectResponse(baseurl('login'));
    }
}
