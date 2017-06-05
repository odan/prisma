<?php

namespace App\Controller;

use App\Service\User\UserSession;
use App\Util\Http;
use Cake\Database\Connection;
use League\Plates\Engine;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\SlimException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * AppController
 */
class AppController
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
     * AppController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get('view');
        $this->logger = $container->get('logger');
        $this->user = $container->get('user');
        $this->db = $container->get('db');
    }

    /**
     * Set request.
     *
     * @param Request $request
     * @throws SlimException
     */
    protected function setRequest(Request $request)
    {
        $this->request = $request;
        $this->http = new Http($request);

        // Authentication check
        $attributes = $request->getAttributes();
        $auth = isset($attributes['_auth']) ? $attributes['_auth'] : true;

        if ($auth === true && !$this->user->isValid()) {
            // Redirect to login page
            $response = $this->redirect('/login');
            //$response = (new Response(401))->write('Unauthorized');
            throw new SlimException($request, $response);
        }
    }

    /**
     * Redirect to url
     *
     * @param string $url
     * @return Response Redirect response
     */
    protected function redirect($url, $status = null)
    {
        if (strpos($url, '/') === 0) {
            $url = $this->http->getBaseUrl($url);
        }
        return (new Response())->withRedirect($url, $status);
    }

    /**
     * Returns global assets (js, css).
     *
     * @param array $assets Assets
     * @return array
     */
    protected function getAssets(array $assets = array())
    {
        // Global assets
        $result = [];
        $result[] = 'assets::css/d.css';
        // Customized notifIt with bootstrap colors
        $result[] = 'assets::css/notifIt-app.css';
        $result[] = 'view::Index/css/layout.css';
        //$files[] = 'Index/css/print.css';
        $result[] = 'assets::js/d.js';
        $result[] = 'assets::js/notifIt.js';
        $result[] = 'view::Index/js/app.js';

        if (!empty($assets)) {
            $result = array_merge($result, $assets);
        }
        return $result;
    }

    /**
     * Returns translated text.
     *
     * @param array $text Text
     * @return array
     */
    protected function getText(array $text = array())
    {
        // Default text
        $result = [];
        $result['Ok'] = __('Ok');
        $result['Cancel'] = __('Cancel');
        $result['Yes'] = __('Yes');
        $result['No'] = __('No');

        if (!empty($text)) {
            $result = array_replace_recursive($result, $text);
        }
        return $result;
    }

    /**
     * Get view data.
     *
     * @param array $viewData
     * @return array
     */
    protected function getViewData(array $viewData = [])
    {
        $result = [
            'baseurl' => $this->http->getBaseUrl('/'),
        ];
        if (!empty($viewData)) {
            $result = array_replace_recursive($result, $viewData);
        }
        return $result;
    }

    /**
     * Render template.
     *
     * @param string $name
     * @param array $viewData
     * @return Response
     */
    protected function render($name, array $viewData = array())
    {
        $content = $this->view->render($name, $viewData);
        $response = new Response();
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * Helper to return JSON from a controller.
     *
     * @param mixed $data
     * @return Response
     */
    protected function json($data, $status = null)
    {
        return (new Response())->withJson($data, $status);
    }
}
