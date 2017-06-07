<?php

namespace App\Table;

use App\Entity\UserEntity;
use App\Util\Hydrator;

/**
 * User
 */
class UserTable extends BaseTable
{

    /**
     * Get user by id
     *
     * @param int $id User id
     * @return UserEntity|null A row
     */
    public function findById($id)
    {
        $query = $this->db->newQuery()
            ->select(['id', 'username', 'first_name', 'last_name', 'created', 'updated'])
            ->from('users')
            ->where(['id' => $id]);

        $row = $query->execute()->fetch('assoc');
        $hydrator = new Hydrator();
        return $hydrator->toObject($row, UserEntity::class);
    }

    /**
     * Get all rows
     *
     * @return UserEntity[] Rows
     */
    public function getAll()
    {
        $query = $this->db->newQuery()
            ->select(['*'])
            ->from('users');
        $rows = $query->execute()->fetchAll('assoc');

        $hydrator = new Hydrator();
        return $hydrator->toCollection($rows, UserEntity::class);
    }
}
