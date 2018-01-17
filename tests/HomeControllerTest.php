<?php

namespace App\Test;

/**
 * @coversDefaultClass \App\Controller\HomeController
 */
class HomeControllerTest extends ApiTestCase
{
    /**
     * Test create object.
     *
     * @return void
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

    /**
     * Test create object.
     *
     * @covers ::pingAction
     * @covers ::__construct
     * @return void
     */
    public function testPing()
    {
        $request = $this->createRequest('POST', '/ping');
        $request = $this->withFormData($request, ['username' => 'user', 'password' => 'user']);
        $response = $this->request($request);

        //$html = (string)$response->getBody();
        //$headers = $response->getHeaders();
        //$status = $response->getStatusCode();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json;charset=utf-8', $response->getHeaderLine('Content-Type'));
        $this->assertSame('{"username":"user","password":"user"}', $response->getBody()->__toString());
    }
}
