<?php

namespace App\Controller;

use App\Service\User\UserSession;
use League\Plates\Engine;
use Odan\Database\Connection;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * AppController
 */
abstract class AbstractController
{
    /**
     * @Inject
     * @var Request
     */
    protected $request;

    /**
     * @Inject
     * @var Response
     */
    protected $response;

    /**
     * @Inject
     * @var Connection
     */
    protected $db;

    /**
     * @Inject
     * @var Engine
     */
    protected $view;

    /**
     * @Inject
     * @var UserSession
     */
    protected $user;

    /**
     * @Inject
     * @var LoggerInterface
     */
    protected $logger;

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
            $url = $this->url($url);
        }

        return $this->response->withRedirect($url, $status);
    }

    /**
     * Get read url
     *
     * @param $path
     * @return string
     */
    protected function url($path)
    {
        return $this->request->getAttribute('baseUrl') . ltrim($path, '/');
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
