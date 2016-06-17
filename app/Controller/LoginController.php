<?php

namespace App\Controller;

use App\Service\User\UserSession;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;

/**
 * LoginController.
 */
class LoginController extends AppController
{

    // No auth check for login controller
    protected $authEnabled = false;

    /**
     * User login
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request = null, Response $response = null)
    {
        $app = $this->app($request);

        $user = new UserSession($app);
        $user->logout($app);

        $assets = $this->getAssets();
        $assets[] = 'view::Index/css/login.css';
        $app->view->addData(['assets' => $assets]);
        $app->view->addData(['baseurl' => $request->getAttribute('base_url')]);
        $content = $app->view->render('view::Index/html/login.html.php');
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * User login submit
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function loginSubmit(Request $request = null, Response $response = null)
    {
        $app = $this->app($request);

        $user = new UserSession($app);
        $username = $app->http->post('username');
        $password = $app->http->post('password');
        $result = $user->login($username, $password);
        $url = ($result) ? '/' : 'login';

        return $app->http->redirectBase($url);
    }

    /**
     * User logout
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function logout(Request $request = null, Response $response = null)
    {
        $app = $this->app($request);

        $user = new UserSession($app);
        $user->logout($app);

        return $app->http->redirectBase('/login');
    }



}
