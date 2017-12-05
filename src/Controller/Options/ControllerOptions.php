<?php

namespace App\Controller\Options;

use App\Service\User\Authentication;
use Odan\Database\Connection;
use Psr\Log\LoggerInterface;
use Slim\Router;
use Slim\Views\Twig;

class ControllerOptions
{

    /**
     * @var Router
     */
    public $router;

    /**
     * @var Connection
     */
    public $db;

    /**
     * @var Twig
     */
    public $view;

    /**
     * @var Authentication
     */
    public $user;

    /**
     * @var LoggerInterface
     */
    public $logger;

}