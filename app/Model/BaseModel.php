<?php

namespace App\Model;

use App\Container\AppContainer;

/**
 * Model: Data Access layer
 *
 * This layer provides access to the persistence layer.
 * This layer is only ever invoked by Service objects.
 * Objects in the data access layer do not know about each other.
 *
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
