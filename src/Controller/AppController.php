<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;

/**
 * AppController
 */
class AppController
{

    protected $container = null;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Get app container
     *
     * @param Request $request
     * @return \App\Container\ServiceContainer
     */
    public function container(Request $request)
    {
        return $request->getAttribute('container');
    }

}
