<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

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
    public function indexPage(Request $request)
    {
        $this->initAction($request);

        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        $text = $this->getText([
            'Loaded successfully!' => __('Loaded successfully!')
        ]);

        $http = new \App\Util\Http($request);
        $viewData = $this->getViewData($request, [
            'text' => $text,
            'counter' => $counter,
            'ip' => $http->getIp(),
            'url' => $http->getUrl()
        ]);

        // Render template
        return $this->render('view::Index/index-index.html.php', $viewData);
    }

    /**
     * Action (Json)
     *
     * @return Response
     */
    public function load(Request $request)
    {
        $this->initAction($request);
        $data = $request->getParsedBody();
        $result = [
            'message' => __('Loaded successfully!'),
            'data' => $data
        ];
        return $this->json($result);
    }
}
