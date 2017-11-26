<?php

namespace App\Controller;

use App\Service\User\Authentication;
use Odan\Database\Connection;
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
     * @var Request
     */
    protected $request;

    /**
     * @Inject
     * @var Response
     */
    protected $response;

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
        $result = [];
        $result['Ok'] = __('Ok');
        $result['Cancel'] = __('Cancel');
        $result['Yes'] = __('Yes');
        $result['No'] = __('No');

        return $result;
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
     * @param string $name Template file
     * @param array $viewData View data
     * @return Response
     */
    protected function render($name, array $viewData = []): Response
    {
        return $this->twig->render($this->response, $name, $viewData);
    }
}
