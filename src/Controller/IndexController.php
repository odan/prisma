<?php

namespace App\Controller;

use Zend\Diactoros\Response;

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
     * Action (JsonRpc)
     *
     * @return mixed
     */
    public function load()
    {
        $json = $this->getRequest()->getAttribute('json');
        //$params = value($json, 'params');
        $result = [
            'status' => 1
        ];
        return $result;
    }
}
