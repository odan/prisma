<?php

namespace App\Action;

use App\Domain\User\Auth;
use Cake\Database\Connection;
use Odan\Slim\Session\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Container;
use Slim\Router;
use Slim\Views\Twig;

/**
 * AbstractAction (Base class).
 */
abstract class BaseAction
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var Twig
     */
    protected $view;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->db = $container->get(Connection::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->router = $container->get('router');
        $this->auth = $container->get(Auth::class);
        $this->view = $container->get(Twig::class);
        $this->session = $container->get(Session::class);
    }

    /**
     * Get view data.
     *
     * @param mixed[] $viewData [Optional] Add this view data
     *
     * @return mixed[] View data
     */
    protected function getViewData(array $viewData = []): array
    {
        $result = [
            'baseUrl' => $this->router->pathFor('root'),
            'text' => $this->getText(),
        ];
        if (!empty($viewData)) {
            $result = array_replace_recursive($result, $viewData);
        }

        return $result;
    }

    /**
     * Returns default text.
     *
     * @return mixed[] Array with translated text
     */
    protected function getText(): array
    {
        $text = [];
        $text['Ok'] = __('Ok');
        $text['Cancel'] = __('Cancel');
        $text['Yes'] = __('Yes');
        $text['No'] = __('No');

        return $text;
    }

    /**
     * Render template.
     *
     * @param ResponseInterface $response
     * @param string $name Template file
     * @param mixed[] $viewData View data
     *
     * @return ResponseInterface
     */
    protected function render(ResponseInterface $response, string $name, array $viewData = []): ResponseInterface
    {
        return $this->view->render($response, $name, $viewData);
    }
}
