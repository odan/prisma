<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Diactoros\Response\RedirectResponse;

/**
 * IndexController
 */
class IndexController extends AppController
{

    /**
     * Index action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request = null, Response $response = null, $params = null)
    {
        $app = $this->app($request);

        // Add data to template
        $assets = $this->getAssets();
        $assets[] = 'view::Index/js/index.js';

        $text = $this->getTextAssets();
        $text['Loaded successfully!'] = __('Loaded successfully!');
        $jsText = $this->getJsText($text);

        // Increment counter
        $counter = $app->session->get('counter', 0);
        $counter++;
        $app->session->set('counter', $counter);

        $data = [
            'baseurl' => $request->getAttribute('base_url'),
            'assets' => $assets,
            'jstext' => $jsText,
            'content' => 'view::Index/html/index.html.php',
            'counter' => $counter,
        ];

        // Render template
        $content = $app->view->render('view::Layout/html/layout.html.php', $data);

        // Return new response
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * Index action
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function login(Request $request = null, Response $response = null, $params = null)
    {
        $app = $this->app($request);
        $uri = $app->http->getBaseUrl('/');
        $response = new RedirectResponse($uri);
        return $response;
    }

    /**
     * Action (JsonRpc)
     *
     * @param Request $request
     * @param Response $response
     * @param mixed $params
     * @return mixed
     */
    public function load(Request $request = null, Response $response = null, $params = null)
    {
        $json = $request->getAttribute('jsonrpc');
        $params = value($json, 'params');
        $json['result'] = [
            'status' => 1
        ];
        return new JsonResponse($json);
    }
}
