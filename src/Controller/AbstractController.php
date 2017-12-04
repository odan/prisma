<?php

namespace App\Controller;

use App\Service\User\Authentication;
use Odan\Database\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;

/**
 * AbstractController (Base class)
 */
abstract class AbstractController
{
    /**
     * @Inject
     * @var Router
     */
    protected $router;

    /**
     * @Inject
     * @var Connection
     */
    protected $db;

    /**
     * @Inject
     * @var Twig
     */
    protected $twig;

    /**
     * @Inject
     * @var Authentication
     */
    protected $user;

    /**
     * @Inject
     * @var LoggerInterface
     */
    protected $logger;

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
        return $this->twig->render($response, $name, $viewData);
    }
}
