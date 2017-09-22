<?php

namespace App\Controller;

use Slim\Http\Response;

/**
 * IndexController
 */
class HomeController extends AbstractController
{
    /**
     * Index action
     *
     * @return Response
     */
    public function indexPage(): Response
    {
        // Increment counter
        $counter = $this->user->get('counter', 0);
        $counter++;
        $this->user->set('counter', $counter);

        $text = [
            'Loaded successfully!' => __('Loaded successfully!')
        ];

        $viewData = $this->getViewData([
            'text' => $text,
            'counter' => $counter,
            'url' => $this->request->getAttribute('url')
        ]);

        // Render template
        return $this->render('view::Home/home-index.html.php', $viewData);
    }

    /**
     * Action (Json)
     *
     * @return Response Json response
     */
    public function load(): Response
    {
        $data = $this->request->getParsedBody();
        $result = [
            'message' => __('Loaded successfully!'),
            'data' => $data
        ];
        return $this->json($result);
    }
}
