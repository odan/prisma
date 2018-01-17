<?php

namespace App\Test;

use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Session;
use Psr\Container\ContainerInterface as Container;
use ReflectionClass;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

class ApiTestCase extends BaseTestCase
{
    /**
     * @var App|null
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

    /**
     * Get container.
     *
     * @return Container
     */
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
     * Set container entry.
     *
     * @param Container $container
     * @param string $key
     * @param mixed $value
     */
    protected function setContainer(Container $container, string $key, $value)
    {
        $class = new ReflectionClass(\Pimple\Container::class);

        $property = $class->getProperty('frozen');
        $property->setAccessible(true);

        $values = $property->getValue($container);
        unset($values[$key]);
        $property->setValue($container, $values);

        $container[$key] = $value;
    }

    /**
     * Create request.
     *
     * @param string $method
     * @param string $url
     * @return Request
     */
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

    /**
     * Add post data.
     *
     * @param Request $request
     * @param array $data
     * @return Request
     */
    protected function withFormData(Request $request, array $data)
    {
        if(!empty($data)) {
            $request = $request->withParsedBody($data);
        }

        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        return $request;
    }

    /**
     * Add Json data.
     *
     * @param Request $request
     * @param array $data
     * @return Request
     */
    protected function withJson(Request $request, array $data)
    {
        $request->getBody()->write(json_encode($data));
        $request = $request->withHeader('Content-Type', 'application/json');

        return $request;
    }

    /**
     * Make request.
     *
     * @param Request $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    protected function request(Request $request)
    {
        $container = $this->getContainer();
        $this->setContainer($container, 'request', $request);
        $this->setContainer($container, 'response', new Response());
        $response = $this->app->run(true);

        return $response;
    }
}
