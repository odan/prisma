<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * LoginIndexAction.
 */
class LoginIndexAction extends AbstractAction
{

    /**
     * User login
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $viewData = $this->getViewData();

        return $this->render($response, 'Login/login.twig', $viewData);
    }
}
