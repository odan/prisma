<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

/**
 * Action.
 */
class UserLoginIndexAction implements ActionInterface
{
    /**
     * @var Twig
     */
    protected $twig;

    /**
     * Constructor.
     *
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    /**
     * User login.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        return $this->twig->render($response, 'User/user-login.twig');
    }
}
