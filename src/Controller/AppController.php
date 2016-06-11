<?php

namespace App\Controller;

use App\Middleware\AppMiddleware;
use Zend\Diactoros\ServerRequest as Request;

/**
 * AppController
 */
class AppController
{

    /**
     * Get app container
     *
     * @param Request $request
     * @return \App\Container\AppContainer
     */
    public function container(Request $request)
    {
        return $request->getAttribute(AppMiddleware::APP_ATTRIBUTE);
    }

}
