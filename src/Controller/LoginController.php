<?php

namespace App\Controller;

use App\Service\User\UserSession;
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
        $user = $this->getUserSession();
        $user->logout();

        $assets = $this->getAssets();
        $assets[] = 'view::Login/login.css';

        $view = view();
        $viewData = $this->getViewData($this->getRequest());
        $content = $view->render('view::Login/login.html.php', $viewData);

        $response = $this->getResponse();
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
        $request = $this->getRequest();

        $data = $request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $user = $this->getUserSession();
        $result = $user->login($username, $password);
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
        $user = $this->getUserSession();
        $user->logout();

        return RedirectResponse(baseurl('login'));
    }
}
