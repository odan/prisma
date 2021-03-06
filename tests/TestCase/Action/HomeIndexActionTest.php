<?php

namespace App\Test\TestCase\Action;

use App\Test\TestCase\ApiTestCase;

/**
 * Test.
 *
 * @coversDefaultClass \App\Action\HomeIndexAction
 */
class HomeIndexActionTest extends ApiTestCase
{
    /**
     * Verify a non-authenticated user gets redirected to your login page.
     *
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     *
     * @return void
     */
    public function testIndexAction(): void
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->request($request);

        // Assert redirect
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/users/login', $response->getHeaderLine('Location'));
    }

    /**
     * Test.
     *
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     *
     * @return void
     */
    public function testPageNotFound(): void
    {
        $request = $this->createRequest('GET', '/not-existing-page');
        $response = $this->request($request);

        $this->assertContains('<h1>Page Not Found</h1>', (string)$response->getBody());
        $this->assertSame(404, $response->getStatusCode());
    }
}
