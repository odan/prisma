<?php

namespace App\Controller;

use App\Service\User\Locale;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * LoginController.
 */
class LoginController extends AbstractController
{

    /**
     * @var Locale
     */
    protected $locale;

    /**
     * Constructor.
     *
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->locale = $container->get(Locale::class);
    }

    /**
     * User login
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function loginAction(Request $request, Response $response): ResponseInterface
    {
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

        $user = $this->auth->authenticate($username, $password);
        if (!empty($user)) {
            $this->locale->setLanguage($user->locale);
            $url = $this->router->pathFor('root');
        } else {
            $url =  $this->router->pathFor('login');
        }

        return $response->withRedirect($url);
    }

    /**
     * User logout
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface Redirect response
     */
    public function logoutAction(Request $request, Response $response): ResponseInterface
    {
        $this->auth->clearIdentity();
        return $response->withRedirect($this->router->pathFor('login'));
    }
}
