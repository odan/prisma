<?php

namespace App\Table;

use App\Model\User;
use Exception;

/**
 * User Repository
 */
class UserTable extends BaseTable
{

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Returns an array with all entities.
     *
     * @return User[] Array with entities
     */
    public function findAll()
    {
        $users = [];
        foreach ($this->fetchAll() as $row) {
            $users[] = new User($row);
        }

        return $users;
    }

    /**
     * Find entity by id.
     *
     * @param int|string $id The ID
     * @return User|null The entity
     */
    public function findById($id)
    {
        $row = $this->fetchById($id);
        if (empty($row)) {
            return null;
        }

        return new User($row);
    }

    /**
     * Get user by id
     *
     * @param int $id User id
     * @return User A row
     * @throws Exception On error
     */
    public function getById($id)
    {
        if (!$user = $this->findById($id)) {
            throw new Exception(__('User not found: %s', $id));
        }
        return $user;
    }
}
