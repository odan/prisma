<?php

namespace App\Action;

use Odan\Session\Session;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Action.
 */
class HomeIndexAction implements ActionInterface
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * @var Session
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param Twig $twig
     * @param Session $session
     */
    public function __construct(Twig $twig, Session $session)
    {
        $this->twig = $twig;
        $this->session = $session;
    }

    /**
     * Index action.
     *
     * @param Request $request the request
     * @param Response $response the response
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        // Increment counter
        $counter = $this->session->get('counter', 0);
        $this->session->set('counter', $counter++);

        $viewData = [
            'text' => $this->getText(),
            'counter' => $counter,
            'url' => $request->getUri(),
            'secure' => $request->getAttribute('secure') ? __('Yes') : __('No'),
        ];

        // Render template
        return $this->twig->render($response, 'Home/home-index.twig', $viewData);
    }

    /**
     * Translate text.
     *
     * @return string[] Array with translated text
     */
    protected function getText(): array
    {
        return [
            'Loaded successfully!' => __('Loaded successfully!'),
            'Loading...' => __('Loading...'),
            'Hello World' => __('Hello World'),
            'Current user' => __('Current user'),
            'User-ID' => __('User-ID'),
            'Username' => __('Username'),
            'User ID' => __('User ID'),
            'Current time' => __('Current time'),
            'Message' => __('Message'),
            'Selected' => __('Selected'),
        ];
    }
}
