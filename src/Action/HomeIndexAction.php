<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * HomeIndexAction.
 */
class HomeIndexAction extends AbstractAction
{
    /**
     * Index action.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        // Increment counter
        $counter = $this->session->get('counter', 0);
        $counter++;
        $this->session->set('counter', $counter);

        $text = [
            'Loaded successfully!' => __('Loaded successfully!'),
        ];

        $viewData = $this->getViewData([
            'text' => $text,
            'counter' => $counter,
            'url' => $request->getUri(),
            'secure' => $request->getAttribute('secure') ? __('Yes') : __('No'),
        ]);

        // Render template
        return $this->render($response, 'Home/home-index.twig', $viewData);
    }

    /**
     * Returns default text.
     *
     * @return mixed[] Array with translated text
     */
    protected function getText(): array
    {
        $text = parent::getText();

        $text['Current user'] = __('Current user');
        $text['User-ID'] = __('User-ID');
        $text['Username'] = __('Username');
        $text['Its'] = __("It's");

        return $text;
    }
}
