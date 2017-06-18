<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * IndexController
 */
class HomeController extends AppController
{
    /**
     * Index action
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response
     */
    public function indexPage(Request $request, Response $response)
    {
        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        $text = $this->getText([
            'Loaded successfully!' => __('Loaded successfully!')
        ]);

        $viewData = $this->getViewData($request, [
            'text' => $text,
            'counter' => $counter,
            'url' => $request->getAttribute('url')
        ]);

        // Render template
        return $this->render($response, 'view::Home/home-index.html.php', $viewData);
    }

    /**
     * Action (Json)
     *
     * @param Request $request The request
     * @param Response $response The response
     * @return Response Json response
     */
    public function load(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $result = [
            'message' => __('Loaded successfully!'),
            'data' => $data
        ];
        return $this->json($response, $result);
    }
}
