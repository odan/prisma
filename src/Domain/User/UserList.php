<?php

namespace App\Domain\User;

class UserList
{
    /**
     * @var UserListRepository
     */
    private $repository;

    /**
     * Constructor.
     *
     * @param UserListRepository $repository The repository
     */
    public function __construct(UserListRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Find all users.
     *
     * @param array $params the parameters
     *
     * @return array the result
     */
    public function listAllUsers(array $params): array
    {
        $result = $this->repository->getTableData($params);

        return $result;
    }
}
