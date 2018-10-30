<?php

namespace App\Test;

use Exception;
use Odan\Slim\Session\Adapter\MemorySessionAdapter;
use Odan\Slim\Session\Session;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use Slim\App;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

/**
 * Class ApiTestCase.
 */
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
    }

    /**
     * Create request.
     *
     * @param string $method
     * @param string $url
     *
     * @return Request
     */
    protected function createRequest(string $method, string $url): Request
    {
        $env = Environment::mock();
        $uri = Uri::createFromString($url);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = (array)UploadedFile::createFromEnvironment($env);
        $request = new Request($method, $uri, $headers, $cookies, $serverParams, $body, $uploadedFiles);

        return $request;
    }

    /**
     * Add post data.
     *
     * @param Request $request The request
     * @param mixed[] $data The data
     *
     * @return Request
     */
    protected function withFormData(Request $request, array $data): Request
    {
        if (!empty($data)) {
            $request = $request->withParsedBody($data);
        }

        $request = $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');

        return $request;
    }

    /**
     * Add Json data.
     *
     * @param Request $request The request
     * @param mixed[] $data The data
     *
     * @return Request
     */
    protected function withJson(Request $request, array $data): Request
    {
        $request->getBody()->write(json_encode($data) ?: '{}');
        $request = $request->withHeader('Content-Type', 'application/json');

        return $request;
    }

    /**
     * Make request.
     *
     * @param Request $request The request
     *
     * @throws Exception
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     *
     * @return ResponseInterface
     */
    protected function request(Request $request): ResponseInterface
    {
        $container = $this->getContainer();
        $this->setContainer($container, 'request', $request);
        $this->setContainer($container, 'response', new Response());

        if ($this->app === null) {
            throw new RuntimeException('App must be initialized');
        }

        $response = $this->app->run(true);

        return $response;
    }

    /**
     * Get container.
     *
     * @throws \ReflectionException
     *
     * @return Container
     */
    public function getContainer(): Container
    {
        if ($this->app === null) {
            throw new RuntimeException('App must be initialized');
        }

        $container = $this->app->getContainer();

        $this->setContainer($container, Session::class, call_user_func(function () {
            $session = new Session(new MemorySessionAdapter());
            $session->setOptions([
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
     *
     * @throws \ReflectionException
     *
     * @return void
     */
    protected function setContainer(Container $container, string $key, $value): void
    {
        $class = new ReflectionClass(\Pimple\Container::class);

        $property = $class->getProperty('frozen');
        $property->setAccessible(true);

        $values = $property->getValue($container);
        unset($values[$key]);
        $property->setValue($container, $values);

        // The same like '$container[$key] = $value;' but compatible with phpstan
        $method = new ReflectionMethod(\Pimple\Container::class, 'offsetSet');
        $method->invoke($container, $key, $value);
    }
}
