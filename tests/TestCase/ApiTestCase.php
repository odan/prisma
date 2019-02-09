<?php

namespace App\Test\TestCase;

use App\Test\Base\BaseTestCase;
use Exception;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
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
    use SlimAppTestTrait;

    /** {@inheritdoc} */
    protected function setUp()
    {
        $this->bootSlim();
    }

    /** {@inheritdoc} */
    protected function tearDown()
    {
        $this->shutdownSlim();
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
}
