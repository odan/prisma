<?php

namespace App\Domain\User;

use App\Repository\BaseRepository;

/**
 * Repository.
 */
class AuthRepository extends BaseRepository
{
    /**
     * Find active user by username.
     *
     * @param string $username The username
     *
     * @return array The user row
     */
    public function findUserByUsername(string $username): array
    {
        $query = $this->newSelect('users')->select('*');
        $query->andWhere(['username' => $username, 'enabled' => 1]);

        $row = $query->execute()->fetch('assoc');

        return $row ?: [];
    }
}
