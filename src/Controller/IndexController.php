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
        $this->setRequest($request);

        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        $text = $this->getText([
            'Loaded successfully!' => __('Loaded successfully!')
        ]);

        $viewData = $this->getViewData([
            'text' => $text,
            'counter' => $counter,
            'ip' => $this->http->getIp(),
            'url' => $this->http->getUrl()
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
        $this->setRequest($request);
        $data = $request->getParsedBody();
        $result = [
            'message' => __('Loaded successfully!'),
            'data' => $data
        ];
        return $this->json($result);
    }
}
