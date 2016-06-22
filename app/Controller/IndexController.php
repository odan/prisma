<?php

namespace App\Controller;

use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

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
    public function index(Request $request = null, Response $response = null)
    {
        $app = $this->app($request);

        // Increment counter
        $counter = $app->session->get('counter', 0);
        $counter++;
        $app->session->set('counter', $counter);

        // Add data to template
        $assets = $this->getAssets([
            'view::Index/js/index.js',
        ]);

        $text = $this->getText([
            'Loaded successfully!' => __('Loaded successfully!')
        ]);

        $data = $this->getData($request, [
            'assets' => $assets,
            'text' => $text,
            'content' => 'view::Index/html/index.html.php',
            'counter' => $counter,
        ]);

        // Render template
        $content = $app->view->render('view::Layout/html/layout.html.php', $data);
        $response->getBody()->write($content);
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
    public function load(Request $request = null, Response $response = null)
    {
        $json = $request->getAttribute('jsonrpc');
        $params = value($json, 'params');
        $json['result'] = [
            'status' => 1
        ];
        return new JsonResponse($json);
    }
}
