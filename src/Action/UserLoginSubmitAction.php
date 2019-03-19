<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\Locale;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Action.
 */
class UserLoginSubmitAction implements ActionInterface
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Constructor.
     *
     * @param Router $router
     * @param Auth $auth
     * @param Locale $locale
     */
    public function __construct(Router $router, Auth $auth, Locale $locale)
    {
        $this->router = $router;
        $this->auth = $auth;
        $this->locale = $locale;
    }

    /**
     * User login submit.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $data = (array)$request->getParsedBody();
        $username = $data['username'];
        $password = $data['password'];

        $user = $this->auth->authenticate($username, $password);

        if (!empty($user) && $user->getLocale() !== null) {
            $this->locale->setLanguage($user->getLocale());
            $url = $this->router->pathFor('root');
        } else {
            $url = $this->router->pathFor('login');
        }

        return $response->withRedirect($url);
    }
}
