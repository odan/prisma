<?php

namespace App\Domain\User;

use App\Repository\QueryFactory;
use App\Repository\RepositoryInterface;

/**
 * Repository.
 */
class AuthRepository implements RepositoryInterface
{
    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * Constructor.
     *
     * @param QueryFactory $queryFactory the query factory
     */
    public function __construct(QueryFactory $queryFactory)
    {
        $this->queryFactory = $queryFactory;
    }

    /**
     * Find active user by username.
     *
     * @param string $username The username
     *
     * @return array The user row
     */
    public function findUserByUsername(string $username): array
    {
        $query = $this->queryFactory->newSelect('users')->select('*');
        $query->andWhere(['username' => $username, 'enabled' => 1]);

        return $query->execute()->fetch('assoc') ?: [];
    }
}
