<?php

namespace App\Repository;

use App\Model\User;
use Exception;

/**
 * User Repository
 */
class UserRepository extends BaseRepository
{

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Get user by id
     *
     * @param int $id User id
     * @return User A row
     * @throws Exception On error
     */
    public function getUserById($id)
    {
        if (!$row = $this->findById($id)) {
            throw new Exception(__('User not found: %s', $id));
        }
        return new User((array)$row);
    }

    /**
     * Get all rows
     *
     * @return User[] Rows
     */
    public function getAllUsers()
    {
        $result = [];
        foreach ($this->findAll() as $row) {
            $result[] = new User($row);
        }
        return $result;
    }
}
