<?php

namespace App\Model;

use App\Container\AppContainer;

/**
 * BaseModel
 */
class BaseModel
{

    /**
     * App Service Container
     *
     * @var AppContainer
     */
    protected $app;

    /**
     * Constructor
     *
     * @param AppContainer $app
     */
    public function __construct(AppContainer $app)
    {
        $this->app = $app;
    }
}
