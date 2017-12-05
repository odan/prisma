<?php

namespace App\Controller;

use App\Controller\Options\ControllerOptions;
use App\Service\User\Authentication;
use Odan\Database\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Router;
use Slim\Views\Twig;

/**
 * AbstractController (Base class)
 */
abstract class AbstractController
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
     * @var Authentication
     */
    protected $user;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param ControllerOptions $options
     */
    public function __construct(ControllerOptions $options)
    {
        $this->db = $options->db;
        $this->logger = $options->logger;
        $this->router = $options->router;
        $this->user = $options->user;
        $this->view = $options->view;
    }

    /**
     * Returns default text.
     *
     * @return array Array with translated text
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
     * Get view data.
     *
     * @param array $viewData
     * @return array View data
     */
    protected function getViewData(array $viewData = []): array
    {
        $result = [
            'baseUrl' => $this->router->pathFor('root'),
            'text' => $this->getText()
        ];
        if (!empty($viewData)) {
            $result = array_replace_recursive($result, $viewData);
        }

        return $result;
    }

    /**
     * Render template.
     *
     * @param ResponseInterface $response
     * @param string $name Template file
     * @param array $viewData View data
     * @return ResponseInterface
     */
    protected function render(ResponseInterface $response, $name, array $viewData = []): ResponseInterface
    {
        return $this->view->render($response, $name, $viewData);
    }
}
