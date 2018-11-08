<?php

namespace App\Service\User;

use App\Repository\ApplicationRepository;

/**
 * Class.
 */
class AuthRepository extends ApplicationRepository
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
