<?php

namespace App\Controller;

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

        $viewData = $this->getViewData([
            'text' => $text,
            'counter' => $counter,
        ]);

        // Render template
        return $this->render('view::Index/index-index.html.php', $viewData);
    }

    /**
     * Action (Json)
     *
     * @return JsonResponse
     */
    public function load()
    {
        $data = $this->getRequest()->getAttribute('data');
        $result = [
            'message' => __('Loaded successfully!'),
            'data' => $data
        ];
        return $this->json($result);
    }
}
