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
     * @return Response
     */
    public function indexPage()
    {
        // Increment counter
        $session = session();
        $counter = $session->get('counter', 0);
        $counter++;
        $session->set('counter', $counter);

        $text = $this->getText([
            'Loaded successfully!' => __('Loaded successfully!')
        ]);

        $viewData = $this->getData($this->getRequest(), [
            'text' => $text,
            'counter' => $counter,
        ]);

        // Render template
        $content = view()->render('view::Index/index-index.html.php', $viewData);

        $response = $this->getResponse();
        $response->getBody()->write($content);
        return $response;
    }

    /**
     * Action (JsonRpc)
     *
     * @return mixed
     */
    public function load()
    {
        $json = $this->getRequest()->getAttribute('json');
        $params = value($json, 'params');
        $result = [
            'status' => 1
        ];
        return $result;
    }
}
