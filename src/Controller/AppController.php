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
    public function app(Request $request)
    {
        return $request->getAttribute(AppMiddleware::ATTRIBUTE);
    }

}
