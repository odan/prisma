<?php

namespace App\Action;

use App\Repository\UserRepository;
use Interop\Container\Exception\ContainerException;
use Psr\Http\Message\ResponseInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * UserIndexAction.
 */
class UserIndexAction extends AbstractAction
{
    /**
     * @var UserRepository
     */
    protected $userRepo;

    /**
     * Constructor.
     *
     * @param Container $container
     *
     * @throws ContainerException
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->userRepo = $container->get(UserRepository::class);
    }

    /**
     * Index.
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface The new response
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {
        $users = $this->userRepo->findAll();

        $viewData = $this->getViewData([
            'users' => $users,
        ]);

        return $this->render($response, 'User/user-index.twig', $viewData);
    }
}
