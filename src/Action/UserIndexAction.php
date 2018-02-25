<?php

namespace App\Action;

use App\Table\UserTable;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * UserIndexAction
 */
class UserIndexAction extends AbstractAction
{
    /**
     * @var UserTable
     */
    protected $userTable;

    /**
     * Constructor.
     *
     * @param Container $container
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userTable = $container->get(UserTable::class);
    }

    /**
     * Index
     *
     * @param Request $request
     * @param Response $response
     * @return ResponseInterface The new response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $users = $this->userTable->findAll();

        $viewData = $this->getViewData([
            'users' => $users
        ]);

        return $this->render($response, 'User/user-index.twig', $viewData);
    }
}
