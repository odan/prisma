<?php

namespace App\Test;

/**
 * @coversDefaultClass \App\Action\HomePingAction
 */
class HomePingActionTest extends ApiTestCase
{
    /**
     * Test create object.
     *
     * @throws \Exception
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     *
     * @return void
     * @covers ::__construct
     * @covers ::__invoke
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
