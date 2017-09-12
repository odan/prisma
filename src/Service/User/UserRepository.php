<?php

namespace App\Service\User;

use App\Model\User;
use App\Table\UserTable;

/**
 * User Repository
 */
class UserRepository extends UserTable
{

    /**
     * Find user by username.
     *
     * @param $username Username
     * @return User|null User
     */
    public function findByUsername($username)
    {
        $row = $this->query()->select('*')
            ->where(['username' => $username, 'disabled' => 0])
            ->execute()
            ->fetch('assoc');

        if (empty($row)) {
            return null;
        }

        return new User($row);
    }
}
