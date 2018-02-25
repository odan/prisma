<?php

namespace App\Action;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * UserReviewAction
 */
class UserReviewAction extends AbstractAction
{

    /**
     * User review page.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return ResponseInterface Response
     */
    public function __invoke(Request $request, Response $response, $args): ResponseInterface
    {
        $id = $args['id'];

        $response->getBody()->write("Action: Show all reviews of user: $id<br>");
        return $response;
    }
}
