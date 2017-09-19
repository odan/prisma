<?php

namespace App\Controller;

use App\Service\User\UserSession;
use App\Utility\Http;
use Cake\Database\Connection;
use League\Plates\Engine;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * AppController
 */
abstract class BaseController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Http
     */
    protected $http;

    /**
     * @var Engine
     */
    protected $view;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UserSession
     */
    protected $user;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * Constructor.
     *
     * @param Engine $view
     * @param Connection $db
     * @param LoggerInterface $logger
     * @param UserSession $user
     */
    public function __construct(Engine $view, Connection $db, UserSession $user, LoggerInterface $logger)
    {
        $this->view = $view;
        $this->db = $db;
        $this->user = $user;
        $this->logger = $logger;
    }

    /**
     * Redirect to url
     *
     * @param Response $response The response
     * @param Request $request
     * @param string $url The URL
     * @param int $status HTTP status code
     * @return Response Redirect response
     */
    protected function redirect(Request $request, Response $response, $url, $status = null): Response
    {
        if (strpos($url, '/') === 0) {
            $url = $request->getAttribute('hostUrl') . $url;
        }
        return $response->withRedirect($url, $status);
    }

    /**
     * Returns default text.
     *
     * @return array Array with translated text
     */
    protected function getText(): array
    {
        $result = [];
        $result['Ok'] = __('Ok');
        $result['Cancel'] = __('Cancel');
        $result['Yes'] = __('Yes');
        $result['No'] = __('No');
        return $result;
    }

    /**
     * Get view data.
     *
     * @param Request $request
     * @param array $viewData
     * @return array View data
     */
    protected function getViewData(Request $request, array $viewData = []): array
    {
        $result = [
            'baseUrl' => $request->getAttribute('baseUrl'),
            'text' => $this->getText()
        ];
        if (!empty($viewData)) {
            $result = array_replace_recursive($result, $viewData);
        }
        return $result;
    }

    /**
     * Render template.
     *
     * @param Response $response The response
     * @param string $name Template file
     * @param array $viewData View data
     * @return Response
     */
    protected function render(Response $response, $name, array $viewData = array()): Response
    {
        $content = $this->view->render($name, $viewData);
        $body = $response->getBody();
        $body->rewind();
        $body->write($content);
        return $response;
    }

    /**
     * Helper to return JSON from a controller.
     *
     * @param Response $response The response
     * @param mixed $data Data
     * @param int $status HTTP status code (200 = OK, 422 Unprocessable entity / validation failed)
     * @return Response
     */
    protected function json(Response $response, $data, $status = null): Response
    {
        return $response->withJson($data, $status);
    }
}
