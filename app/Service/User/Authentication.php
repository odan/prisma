<?php

namespace App\Service\User;

use App\Container\AppContainer;
use App\Service\Base\BaseService;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * Authentication
 */
class Authentication extends BaseService
{
    /**
     * Check user session login
     *
     * @param Request $request
     * @param Response $response
     * @return bool|Response
     */
    public static function check(Request $request, Response $response, $vars, $action, $callback)
    {
        /* @var $app AppContainer */
        $app = $request->getAttribute(\App\Middleware\AppMiddleware::ATTRIBUTE);

        if (!static::isAuthRequired($app, $action)) {
            return true;
        }

        $userSession = new UserSession($app);
        if ($userSession->isValid()) {
            return true;
        } else {
            $http = new \App\Util\Http($request, $response);
            if ($http->isJsonRpc()) {
                $json = new \App\Util\JsonServer($request, $response);
                $jsonContent = $json->getResponseByError('Unauthorized', 0, 0, 401);
                return $jsonContent;
            } else {
                $uri = $app->http->getBaseUrl('/login');
                return new RedirectResponse($uri);
                // alternative would be
                // new HtmlResponse('401 Unauthorized', 401);
            }
        }
        return true;
    }

    /**
     * Check if auth is required
     *
     * @param AppContainer $app
     * @param string $action
     * @return boolean
     */
    protected static function isAuthRequired(AppContainer $app, $action)
    {
        if (empty($app->config['router']['noauth'])) {
            return true;
        }
        $whitelist = $app->config['router']['noauth'];
        return !in_array($action, $whitelist);
    }
}
