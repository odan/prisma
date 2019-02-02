<?php

namespace App\Domain\User;

use App\Repository\BaseRepository;

/**
 * Class.
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
        $query->andWhere(['username' => $username, 'disabled' => 0]);

        $row = $query->execute()->fetch('assoc');

        return $row ?: [];
    }
}
