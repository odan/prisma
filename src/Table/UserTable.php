<?php

namespace App\Table;

use App\Entity\UserEntity;

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
     */
    public function findById($id)
    {
        if (!$row = parent::findById($id)) {
            return null;
        }
        return new UserEntity($row);
    }

    /**
     * Get all rows
     *
     * @return UserEntity[] Rows
     */
    public function getAll()
    {
        $result = [];
        foreach (parent::getAll() as $row) {
            $result[] = new UserEntity($row);
        }
        return $result;
    }
}
