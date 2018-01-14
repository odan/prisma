<?php

namespace App\Test;

use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Session;
use ReflectionClass;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;
use Slim\Container;

class ApiTestCase extends BaseTestCase
{
    /**
     * @var App
     */
    protected $app;

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->app = require __DIR__ . '/../config/bootstrap.php';
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->app = null;

        // Delete old session
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();
    }

    public function getContainer()
    {
        $container = $this->app->getContainer();

        $this->setContainer($container, Session::class, call_user_func(function () {
            $session = new Session(new MemorySessionAdapter());
            $session->setConfig([
                'cache_expire' => 60,
                'name' => 'app',
                'use_cookies' => false,
                'cookie_httponly' => false,
            ]);

            return $session;
        }));

        return $container;
    }

    /**
     * @param Container $container
     * @param $key
     * @param $value
     */
    protected function setContainer(Container $container, $key, $value)
    {
        $class = new ReflectionClass(\Pimple\Container::class);

        $property = $class->getProperty('frozen');
        $property->setAccessible(true);

        $values = $property->getValue($container);
        unset($values[$key]);
        $property->setValue($container, $values);

        $container->offsetSet($key, $value);
    }

    protected function createRequest(string $method, string $url)
    {
        $env = Environment::mock();
        $uri = Uri::createFromString($url);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = UploadedFile::createFromEnvironment($env);
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

        return $request;
    }

    protected function withPost(Request $request, array $data)
    {
        $request->getBody()->write(http_build_query($data));
        $request->getBody()->rewind();
        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        return $request;
    }

    protected function withJson(Request $request, array $data)
    {
        $request->getBody()->write(json_encode($data));
        $request->getBody()->rewind();
        $request = $request->withHeader('Content-Type', 'application/json');

        return $request;
    }

    protected function request(Request $request)
    {
        $container = $this->getContainer();
        $this->setContainer($container, 'request', $request);
        $this->setContainer($container, 'response', new Response());
        $response = $this->app->run(true);

        return $response;
    }

}