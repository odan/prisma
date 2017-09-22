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
abstract class AbstractController
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

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
     * Constructor.
     *
     * @param Request $request The request
     * @param Response $response The response
     * @param Connection $db
     * @param Engine $view
     * @param LoggerInterface $logger
     * @param UserSession $user
     */
    public function __construct(Request $request, Response $response, Connection $db, Engine $view, UserSession $user, LoggerInterface $logger)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = $view;
        $this->db = $db;
        $this->user = $user;
        $this->logger = $logger;
    }

    /**
     * @var UserSession
     */
    protected $user;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * Redirect to url
     *
     * @param string $url The URL
     * @param int $status HTTP status code
     * @return Response Redirect response
     */
    protected function redirect($url, $status = null): Response
    {
        if (strpos($url, '/') === 0) {
            $url = $this->request->getAttribute('hostUrl') . $url;
        }
        return $this->response->withRedirect($url, $status);
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
     * @param array $viewData
     * @return array View data
     */
    protected function getViewData(array $viewData = []): array
    {
        $result = [
            'baseUrl' => $this->request->getAttribute('baseUrl'),
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
     * @param string $name Template file
     * @param array $viewData View data
     * @return Response
     */
    protected function render($name, array $viewData = array()): Response
    {
        $content = $this->view->render($name, $viewData);
        $body = $this->response->getBody();
        $body->rewind();
        $body->write($content);
        return $this->response;
    }

    /**
     * Helper to return JSON from a controller.
     *
     * @param mixed $data Data
     * @param int $status HTTP status code (200 = OK, 422 Unprocessable entity / validation failed)
     * @return Response
     */
    protected function json($data, $status = null): Response
    {
        return $this->response->withJson($data, $status);
    }
}
