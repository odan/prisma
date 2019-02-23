<?php

namespace App\Action;

use App\Domain\User\Auth;
use App\Domain\User\Locale;
use App\Factory\ContainerFactory;
use Cake\Database\Connection;
use Interop\Container\Exception\ContainerException;
use Odan\Slim\Session\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Container;
use Slim\Router;
use Slim\Views\Twig;

/**
 * Action base class
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
     * @var Locale
     */
    protected $locale;

    /**
     * @var ContainerFactory
     */
    protected $factory;

    /**
     * Constructor.
     *
     * @param Container $container
     *
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        $this->db = $container->get(Connection::class);
        $this->logger = $container->get(LoggerInterface::class);
        $this->router = $container->get('router');
        $this->view = $container->get(Twig::class);
        $this->auth = $container->get(Auth::class);
        $this->session = $container->get(Session::class);
        $this->locale = $container->get(Locale::class);
        $this->factory = $container->get(ContainerFactory::class);
    }

    /**
     * Returns default text.
     *
     * @return mixed[] Array with translated text
     */
    protected function getDefaultText(): array
    {
        return [
            'Ok' => __('Ok'),
            'Cancel' => __('Cancel'),
            'Yes' => __('Yes'),
            'No' => __('No'),

        ];
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
        $viewData = array_replace_recursive([
            'baseUrl' => $this->router->pathFor('root'),
            'text' => $this->getDefaultText(),
        ], $viewData);

        return $this->view->render($response, $name, $viewData);
    }
}
