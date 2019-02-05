<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Action.
 */
class UserLoginSubmitAction extends BaseAction
{
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
