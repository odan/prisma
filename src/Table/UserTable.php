<?php

namespace App\Table;

use App\Entity\UserEntity;
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
     * Get user by id
     *
     * @param int $id User id
     * @return UserEntity|null A row
     * @throws Exception On error
     */
    public function getUserById($id)
    {
        if (!$row = parent::findById($id)) {
            throw new Exception(__('User not found: %s', $id));
        }
        return new UserEntity($row);
    }

    /**
     * Get all rows
     *
     * @return UserEntity[] Rows
     */
    public function getAllUsers()
    {
        $result = [];
        foreach (parent::getAll() as $row) {
            $result[] = new UserEntity($row);
        }
        return $result;
    }
}
