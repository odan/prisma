<?php

namespace App\Test;

/**
 * @coversDefaultClass \App\Action\HomeIndexAction
 */
class HomeIndexActionTest extends ApiTestCase
{
    /**
     * Test create object.
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

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->getHeaderLine('Location'));
    }

    /**
     * Test.
     *
     * @return void
     *
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testPageNotFound(): void
    {
        $request = $this->createRequest('GET', '/not-existing-page');
        $response = $this->request($request);

        $this->assertContains('<h1>Page Not Found</h1>', (string)$response->getBody());
        $this->assertSame(404, $response->getStatusCode());
    }
}
