<?php

namespace App\Container;

use Cake\Database\Connection;
use League\Plates\Engine;
use Symfony\Component\Translation\Translator;

/**
 * Application service container
 */
class AppContainer
{

    /**
     * Configuration
     *
     * @var array
     */
    public $config = null;

    /**
     * Database connection
     *
     * @var Connection
     */
    public $db = null;

    /**
     * View template engine
     *
     * @var Engine
     */
    public $view = null;

    /**
     * Translator
     *
     * @var Translator
     */
    public $translator = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = array();
    }

    /**
     * Read php file
     *
     * @param string $file Filename
     * @return mixed
     */
    public function read($file)
    {
        return require $file;
    }
}
