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
        $user = user();
        $counter = $user->get('counter', 0);
        $counter++;
        $user->set('counter', $counter);

        $text = $this->getText([
            'Loaded successfully!' => __('Loaded successfully!')
        ]);

        $viewData = $this->getViewData([
            'text' => $text,
            'counter' => $counter,
            'ip' => http()->getIp(),
            'url' => http()->getUrl()
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
        $data = request()->getAttribute('data');
        $result = [
            'message' => __('Loaded successfully!'),
            'data' => $data
        ];
        return $this->json($result);
    }
}
