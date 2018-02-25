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
     * @covers ::__construct
     * @covers ::__invoke
     * @return void
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function testIndexAction()
    {
        $request = $this->createRequest('GET', '/');
        $response = $this->request($request);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('/login', $response->getHeaderLine('Location'));
    }

    /**
     * Test
     *
     * @coversNothing
     */
    public function testPageNotFound()
    {
        $request = $this->createRequest('GET', '/not-existing-page');
        $response = $this->request($request);

        $this->assertContains('<h1>Page Not Found</h1>', (string)$response->getBody());
        $this->assertSame(404, $response->getStatusCode());
    }
}
