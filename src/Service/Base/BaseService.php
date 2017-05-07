<?php

namespace App\Service\Base;

use App\Container\AppContainer;

/**
 * Model: Service layer
 *
 * This layer provides cohesive, high-level logic for related
 * parts of an application. This layer is invoked directly by
 * the Controller and View helpers.
 *
 * The business logic should be placed in the model service layer,
 * and we should be aiming for fat models and skinny controllers.
 */
class BaseService
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
